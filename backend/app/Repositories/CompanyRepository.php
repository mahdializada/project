<?php


namespace App\Repositories;

use Exception;
use App\Models\Folder;
use App\Models\System;
use App\Events\Updated;
use App\Models\Company;
use App\Traits\FileTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Symfony\Component\HttpFoundation\File\File;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Repositories\Files\System as Filesystem;

class CompanyRepository extends Repository
{

    use FileTrait;
    private $filePath = "companies/logos";
    protected $applicationFileManagement = 'application_file_management';


    public function paginateCompanies(Request $request)
    {
        $companyQuery = Company::where('status', 'active')->orderBy("name", "ASC");
        if (!$request->user()->functional) {
            $companyQuery =  $companyQuery->whereHas('users', fn (Builder $builder)
            => $builder->where('users.id', $request->user()->id));
        }
        $companies = $companyQuery->select(['id', 'name', 'code', 'logo'])->get();
        return response()->json($companies);
    }

    public function paginate($request): JsonResponse
    {
        if ($request->query->has('country_ids')) {
            return $this->getCompanyBasedOnCountryIds($request);
        } else if ($request->query->has('key') && $request->get('key') == 'only-companies') {
            return $this->getAllowedCompaniesOfCountries($request);
        } else if ($request->query->has('key') && $request->get('key') == 'logged-in-company') {
            return $this->getAllowedCompaniesOfCountries($request);
        } else {
            $key = $request->query->get("key");
            return  $this->getCompanyAccordingToStatus($request, $key);
        }
    }

    public function getAllowedCompaniesOfCountries(Request $request)
    {
        $countryIds = $request->get('country_ids');
        $companies = Company::whereHas('users', function (Builder $builder) use ($request) {
            $builder->where('users.id', $request->user()->id);
        });
        if (!is_null($countryIds) && count($countryIds)) {
            $companies = $companies->whereHas('countries', function (Builder $builder) use ($countryIds) {
                $builder->whereIn('countries.id', $countryIds);
            });
        }
        $companies = $companies->where('status', 'active')
            ->get(['id', 'name', 'code', 'logo'])
            ->toArray();
        return response()->json(['companies' => $companies]);
    }

    private function  getCompanyAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Company(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();

        $searchInColumns    = ['name', 'email', 'status', 'investment_type', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $companyData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'warningTotal, status, warning',
        ];
        $companyData = $this->getStatusCount($statusQuery, $companyData, $extraData);
        return response()->json($companyData);
    }

    public function search($request)
    {
        $queryBuilder = new UriQueryBuilder(new Company(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }


    public function getCompanyBasedOnCountryIds($request): JsonResponse
    {
        $country_ids = $request->query->get('country_ids');
        $companies = Company::with(['countries:id,code,name,iso2'])
            ->whereHas(
                'countries',
                function (Builder $query) use ($country_ids) {
                    $query->whereIn('country_id', $country_ids);
                }
            )
            ->where("status", "active")
            ->select(['id', 'name', 'logo', 'code'])
            ->get();
        return \response()->json(['companies' => $companies], Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $logoPath = "";
        try {
            DB::beginTransaction();
            $socialMedias = $request->socialMedias;
            $company = new Company();
            $attributes = $request->only($company->getFillable());
            $attributes['code'] = rand(100000, 999999999);
            $attributes['created_by'] = $request->user()->id;
            $attributes['updated_by'] = $request->user()->id;
            $attributes['domain'] = json_encode($request->domain);
            $this->prefix                       = $this->filePath;
            $logoPath                          = $this->storeFile($request->file('logo'));
            // $logoPath                          = Storage::disk('s3')->store($request->file('logo'));
            $attributes["logo"]                = $logoPath;
            $company = Company::create($attributes);

            // Store file in file management system
            // $storeSystemFile                    = new Filesystem();
            // $filename                           = $request->input("name");
            // $systemFileModel                    = $storeSystemFile->storeSystemFile($filename, 'Companies', $request->file('logo'), $company->id);
            // $filenameSubStr                     = explode('storage/', $systemFileModel->path);
            // $company->logo                      = $filenameSubStr[1];
            // $company->save();

            // $systemFileModel->relateable_type   = Company::class;
            // $systemFileModel->relateable_id     = $company->id;
            // $systemFileModel->save();

            $company->systems()->sync($request->system_ids);
            $company->countries()->sync($request->country_ids);
            if ($socialMedias != null) {
                $allMedia = [];
                foreach ($socialMedias as $socialMedia) {
                    $data = json_decode($socialMedia);
                    $allMedia[$data->id] = ['url' => $data->url];
                }
                $company->socialMedia()->sync($allMedia);
            }
            DB::commit();
            broadcast(new Updated('company', $company->id, 'created'));
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            if ($logoPath) {
                $this->deleteFile($logoPath);
            }
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:2', 'max:100', 'unique:companies,name'],
            'email'           => ['required', 'email', 'unique:companies,email'],
            'logo'            => ['required'],
            "note"            => ['required'],
            "domain"          => ['required'],
            'country_ids'     => ['required', 'exists:countries,id'],
            'system_ids'      => ['required', 'exists:systems,id'],
            'investment_type' => ['required'],
        ];
    }

    public function update(Request $request): JsonResponse
    {
        $newImagePath = null;
        try {
            return DB::transaction(function () use ($request) {
                $company = (object)$request->all();
                $companyData = Company::with($this->getRelations())->findOrFail($company->id);
                if ($company->logo) {
                    if ($company->logo !== '') {
                        // Converting from Base64 to File
                        $base64File = $company->logo;
                        $logoImage = $this->convertToFile($base64File);
                        $this->prefix = $this->filePath;
                        $imagePath = $this->storeAndRemove($logoImage, $companyData->getRawOriginal("logo"));
                        // Saving new Image Path
                        $companyData->logo = $imagePath;
                    }
                }
                if ($company->name) {
                    $companyData->name = $company->name;
                }
                if ($company->email) {
                    $companyData->email = $company->email;
                }
                if ($company->note) {
                    $companyData->note = $company->note;
                }
                if ($company->investment_type) {
                    $companyData->investment_type = $company->investment_type;
                }
                $companyData->updated_by = $request->user()->id;

                $companyData->update();
                if ($company->country_ids) {
                    $companyData->countries()->sync($company->country_ids);
                }
                if ($company->system_ids) {
                    $companyData->systems()->sync($company->system_ids);
                    // $fileManagement = System::where('phrase', $this->applicationFileManagement)->first();
                    // if (in_array($fileManagement->id, $request->system_ids)) {
                    //     foreach ($companyData->systems as $companySystems) {
                    //         foreach ($companySystems->subSystems as $subSystem) {
                    //             if ($subSystem->has_file) {
                    //                 $folder = $this->createFolder($subSystem->name, $companyData, $subSystem, $request);
                    //                 if ($folder->name == 'Design Request') {
                    //                     $this->createFolder('Attachments', $companyData, $subSystem, $request, $folder);
                    //                 }
                    //                 else if ($folder->name == 'Users') {
                    //                     $this->createFolder('Profiles', $companyData, $subSystem, $request, $folder);
                    //                 }
                    //                 else if ($folder->name == 'Companies') {
                    //                     $this->createFolder('Company Logos', $companyData, $subSystem, $request, $folder);
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }
                }
                if ($company->socialMedias != null) {
                    $allMedia = [];
                    foreach ($company->socialMedias as $socialMedia) {
                        $id = $socialMedia->id;
                        $url = $socialMedia->url;
                        $allMedia[$id] = ['url' => $url];
                    }
                    $companyData->socialMedia()->sync($allMedia);
                }
                broadcast(new Updated('company', $companyData->id, 'updated'));
                return $this->successResponse($companyData->load($this->getRelations()), Response::HTTP_ACCEPTED);
            });
        } catch (Exception $exception) {
            dd($exception);
            if ($newImagePath) {
                $this->deleteFile($newImagePath);
            }
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    function createFolder($typeName, $companyData, $subSystem, $request, $folder = null)
    {
        return Folder::firstOrCreate([
            'name' => $typeName,
            'company_id' => $companyData->id,
            'parent_id' => $folder->id,
            'sub_system_id' => $subSystem->id,
            'created_by' => $request->user()->id,
        ]);
    }

    public function convertToFile($base64File)
    {
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));
        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);

        // this just to help us get file info.
        $tmpFile = new File($tmpFilePath);


        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );
        return $file;
    }
    public function updateRules($request)
    {
        $rules = [
            "id"   => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email'           => ['required', 'email'],
            "note" => ['required'],
            'country_ids'      => ['required', 'exists:countries,id'],
            'system_ids'      => ['required', 'exists:systems,id'],
            'investment_type' => ['required'],
        ];
        $request->validate($rules);
    }

    private function getRelations()
    {
        return [
            "createdBy:id,firstname,lastname",
            "updatedBy:id,firstname,lastname",
            "systems:id,name,logo",
            "departments:id,name",
            "countries:id,name,iso2",
            "reasons",
            'changedBy:id,firstname,lastname',
            'socialMedia:id,code,logo,name,website,sample_url_account',
        ];
    }


    // return a specific project if found else return not found
    public function show($id): JsonResponse
    {
        $company = Company::withTrashed()->with($this->getRelations())->find($id);
        if ($company) {
            return $this->successResponse($company);
        }
        return $this->errorResponse("Not Found", Response::HTTP_NOT_FOUND);
    }

    public function changeCompanyStatus(Request $request)
    {

        return $this->changeStatus($request, Company::class, 'companies', 'reason_company', 'company_id', $this->getRelations(), 'company');
    }


    public function destroy(array $ids, $reasonId, $request)
    {
        return $this->destroyItems(
            Company::class,
            $ids,
            'reason_company',
            $reasonId,
            'company_id',
            null,
            $this->getRelations(),
            'company',
            $request
        );
    }

    public function validateCompanyName($request): JsonResponse
    {
        try {
            $name = $request->input('name');
            $company = Company::where('name',  $name)->first();
            if ($company) {
                return $this->errorResponse(Response::HTTP_NOT_FOUND);
            }
            return $this->successResponse(Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function validateCompanyEmail($request): JsonResponse
    {
        return response()->json($request);
        try {
            $email = $request->input('email');
            $company = Company::where('email',  $email)->first();
            if ($company) {
                return $this->errorResponse(Response::HTTP_NOT_FOUND);
            }
            return $this->successResponse(Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    // check the uniqueness of columns and its related values in database
    public function checkUniqueness(Request $request): JsonResponse
    {
        $result = [];
        $columns = $request->all();
        foreach ($columns as $column) {
            $column = collect($column);
            $isExists = Company::where($column->get("name"), $column->get("value"))->exists();
            $result[] = [
                'column' => $column->get("name"),
                'if_exists' => $isExists,
                'column_value' => $column->get('value'),
                'index' => $column->get('index')
            ];
        }
        unset($result['']);
        return response()->json($result);
    }
    public function checkNameUniquenessOnCrud(Request $request): JsonResponse
    {
        $result = [];
        $companyName = $request->get('name');
        $depId = $request->query->get("id");
        $hasDepId = $request->query->has("id");
        if ($hasDepId) {
            $isExists = Company::where('name', $companyName)->where("id", "!=", $depId)->exists();
        } else {
            $isExists = Company::whereName($companyName)->exists();
        }
        $result['name'] = $isExists;

        return response()->json($result);
    }
    public function checkEmailUniquenessOnCrud(Request $request): JsonResponse
    {
        $result = [];
        $email = $request->get('email');
        $depId = $request->query->get("id");
        $hasDepId = $request->query->has("id");
        if ($hasDepId) {
            $isExists = Company::where('email', $email)->where("id", "!=", $depId)->exists();
        } else {
            $isExists = Company::where('email', $email)->exists();
        }
        $result['email'] = $isExists;

        return response()->json($result);
    }


    public static function connectionCompanies()
    {
        $companies = Company::whereHas("connections")->select(["id", "name", "logo"])->get();
        return $companies;
    }
}
