<?php

namespace App\Repositories;

use App\Models\Country;
use App\Models\Location;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Traits\FileTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\SupplierBankAccount;
use Illuminate\Support\Facades\Validator;

class SupplierRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get('key');


        return $this->getSuppliersAccordingToStatus($request, $key);

    }

    private function getSuppliersAccordingToStatus($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();
        if ($key == "deleted") {
            $queryBuilder->query->onlyTrashed();
            $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
            $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
            $supplierData           = $queryBuilder->build()->paginate()->getData();
            $this->getExtraData($supplierData, $request);
            return response()->json($supplierData);
        }
        if ($key != "all") {
            $queryBuilder->query->where("status", $key);
        }
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $supplierData = $queryBuilder->build()->paginate()->getData();
        $this->getExtraData($supplierData, $request);
        return response()->json($supplierData);
    }

    private function getExtraData($extaData, $request)
    {
        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $activeTotal           = $queryBuilder->query;
        $extaData->activeTotal   = $activeTotal->where("status", "active")->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $inactiveTotal           = $queryBuilder->query;
        $extaData->inactiveTotal = $inactiveTotal->where("status", "inactive")->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $warningTotal           = $queryBuilder->query;
        $extaData->warningTotal  = $warningTotal->where("status", "warning")->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $removedTotal           = $queryBuilder->query;
        $extaData->removedTotal  = $removedTotal->where("status", "removed")->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $pendingTotal           = $queryBuilder->query;
        $extaData->pendingTotal  = $pendingTotal->where("status", "pending")->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $blockedTotal           = $queryBuilder->query;
        $extaData->blockedTotal  = $blockedTotal->where("status", "blocked")->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $allTotal           = $queryBuilder->query;
        $extaData->allTotal      = $allTotal->withTrashed()->count();

        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->setWithTrashed();
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        $queryBuilder->query = $this->searchSuppliers($queryBuilder->query, $request);
        $deletedTotal           = $queryBuilder->query;
        $extaData->deletedTotal  = $deletedTotal->onlyTrashed()->count();
        return $extaData;
    }


    private function getRelations(): array
    {
        return [
            // 'sourcers:id,name',
            'companies:id,name,email,logo',
            "locations.country",
            "locations.state",
            // "label:id,title",
            // 'statuses',
            // 'changedBy:id,firstname,lastname',
            'sourcers:id,name',
            'updatedBy:id,firstname,lastname',
            'createdBy:id,firstname,lastname',
        ];
    }
    public function getLocations($id)
    {
        $supplier = Location::with('supplier:id,supplier_name', 'country:id,name,iso2,iso3', 'state:id,name')->where('supplier_id', $id)->orderBy("id","desc")->get();
        return $supplier;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $supplier       = new Supplier();
            $attributes     = $request->only($supplier->getFillable());
            $attributes['code'] = uniqueCode(Supplier::class, "TALS");
            $attributes['created_by'] = Auth::id();
            $attributes['updated_by'] = Auth::id();
            $supplier = Supplier::create($attributes);
            if ($request->has('location') && $request->input('location')) {
                foreach ($request->input('location')  as $key => $location) {
                    $locationDtata = [
                        'country_id' => $location['country_id'],
                        'state_id' => $location['state_id'],
                        'location_type' => $location['location_type'],
                        'address' => $location['address'],
                        'map_link' => $location['map_link'],
                        'location_phone' => $location['location_phone'],
                        'crowd_status' => $location['crowd_status'],
                        'contact_staff_name' => $location['contact_staff_name'],
                        'notes' => $location['notes']??null,
                        'supplier_id' => $supplier->id,
                    ];
                    Location::create($locationDtata);
                }
            }
            if ($request->has('bank') && $request->input('bank')) {
                foreach ($request->input('bank')  as $key => $bank) {
                    $bankAcountData = [
                        'bank_name' => $bank['bank_name'],
                        'bank_account_name' => $bank['bank_account_name'],
                        'bank_account_number' => $bank['bank_account_number'],
                        'bank_account_number_iban' => $bank['bank_account_number_iban'],
                        'swift_code' => $bank['swift_code'],
                        'address' => $bank['address'],
                        'supplier_id' => $supplier->id,
                    ];
                    SupplierBankAccount::create($bankAcountData);
                }
            }

            $supplier->companies()->attach($request->companyIds);
            $supplier->sourcers()->attach($request->sourcerIds);
            DB::commit();
            return $this->successResponse($supplier->load($this->getRelations()), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }


    // public function storeRules(): array
    // {
    //     return [
    //         'supplier_trading_name' => ['required', 'string'],
    //         'supplier_name' => ['required', 'string'],
    //         'main_phone' => ['required', 'string'],
    //         'email'           => ['required', 'email', 'unique:suppliers,email'],
    //         'purchase_order_phone' => ['required', 'string'],
    //         'website' => ['required'],
    //         'prepration_period' => ['required', 'string'],
    //         'supply_type' => ['required', 'string'],
    //         'commercial_type' => ['required', 'string'],
    //         'sourcer' => ['required', 'string'],
    //         'legal_type' => ['required', 'string'],
    //         'country_type' => ['required', 'string'],
    //     ];
    // }
//     public function storeRules(): array
// {
//     $rules = [
//         'supplier_trading_name' => ['required', 'string'],
//         'supplier_name' => ['required', 'string'],
//         'main_phone' => ['required', 'string'],
//         'email' => ['required', 'email', 'unique:suppliers,email'],
//         'purchase_order_phone' => ['required', 'string'],
//         'prepration_period' => ['required', 'string'],
//         'supply_type' => ['required', 'string'],
//         'commercial_type' => ['required', 'string'],
//         'sourcer' => ['required', 'string'],
//         'legal_type' => ['required', 'string'],
//         'country_type' => ['required', 'string'],
//     ];

//     if (request()->input('isRequired', false)) {
//         $rules['website'] = ['required', 'string'];
//     } else {
//         unset($rules['website']);
//     }

//     return $rules;
// }
public function storeRules(): array
{
    $rules = [
        'supplier_trading_name' => ['required', 'string'],
        'supplier_name' => ['required', 'string'],
        'main_phone' => ['required', 'string'],
        'email' => ['required', 'email', 'unique:suppliers,email'],
        'purchase_order_phone' => ['required', 'string'],
        'prepration_period' => ['required', 'string'],
        'supply_type' => ['required', 'string'],
        'commercial_type' => ['required', 'string'],
        // 'sourcerIds' => ['required', 'array'],
        'sourcerIds' => ['required'],
        'legal_type' => ['required', 'string'],
        'country_type' => ['required', 'string'],
    ];
    if (request()->input('isRequired', true)) {
        $rules['website'] = ['required', 'string', 'url'];
    } else {
        $rules['website'] = ['nullable', 'string', 'url'];
    }

    return $rules;
}

    public function update(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $supplier       = Supplier::find($request->supplier_id);
            $attributes = $request->only($supplier->getFillable());
            $attributes['updated_by'] = Auth::id();
            unset($attributes["created_by"]);
            $supplier->update($attributes);
            if ($request->has('location') && $request->input('location')) {
                Location::where('supplier_id',$request->supplier_id)->delete();
                foreach ($request->input('location')  as $key => $location) {
                    $locationDtata = [
                        'country_id' => $location['country_id'],
                        'state_id' => $location['state_id'],
                        'location_type' => $location['location_type'],
                        'address' => $location['address'],
                        'map_link' => $location['map_link'],
                        'location_phone' => $location['location_phone'],
                        'crowd_status' => $location['crowd_status'],
                        'contact_staff_name' => $location['contact_staff_name'],
                        'notes' => $location['notes']??null,
                        'supplier_id' => $request->supplier_id,
                    ];
                    Location::create($locationDtata);
                }
            }
            if ($request->has('bank') && $request->input('bank')) {
                SupplierBankAccount::where('supplier_id',$request->supplier_id)->delete();
                foreach ($request->input('bank')  as $key => $bank) {
                    $bankAcountData = [
                        'bank_name' => $bank['bank_name'],
                        'bank_account_name' => $bank['bank_account_name'],
                        'bank_account_number' => $bank['bank_account_number'],
                        'bank_account_number_iban' => $bank['bank_account_number_iban'],
                        'swift_code' => $bank['swift_code'],
                        'address' => $bank['address'],
                        'supplier_id' => $request->supplier_id,
                    ];
                    SupplierBankAccount::create($bankAcountData);
                }
            }

            $supplier->companies()->sync($request->companyIds);
            // $supplier->sourcers()->sync($request->sourcerIds);
            $supplier = $supplier->load($this->getRelations());
            DB::commit();
            return $this->successResponse($supplier, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    // private function insertLocations($locations, $supplier_id)
    // {
    //     for ($i = 0; $i < count($locations); $i++) {
    //         $location                       = !$locations[$i]['id'] ? new Location() : Location::find($locations[$i]['id']);
    //         $location->location_type        = $locations[$i]['location_type'];
    //         $location->address              = $locations[$i]['address'];
    //         $location->map_link             = $locations[$i]['map_link'];
    //         $location->location_phone       = $locations[$i]['location_phone'];
    //         $location->crowd_status         = $locations[$i]['crowd_status'];
    //         $location->notes                = $locations[$i]['notes'];
    //         $location->country_id           = $locations[$i]['country_id'];
    //         $location->state_id             = $locations[$i]['state_id'];
    //         $location->supplier_id          = $supplier_id;
    //         $location->contact_staff_name   = $locations[$i]['contact_staff_name'];
    //         $location->save();
    //     }
    // }

    public function updateRules($request)
    {
        $rules = [
            "supplier_id"                        => ['required', 'exists:suppliers,id'],
            'supplier_trading_name'     => ['required', 'string', 'min:2', 'max:100'],
            'supplier_name'             => ['required', 'string', 'min:2'],
            "email"                     => ['required', 'email', 'unique:suppliers,email,' . $request->supplier_id],
            'main_phone'                => ['required', 'string', 'unique:suppliers,main_phone,' . $request->supplier_id],
            'purchase_order_phone'      => ['required', 'string', 'unique:suppliers,purchase_order_phone,' . $request->supplier_id],
            'website'                   => ['required', 'string'],
            'prepration_period'         => ['required', 'string'],
            'supply_type'               => ['required', 'string'],
            'sourcer'                   => ['required', 'string'],
            'commercial_type'           => ['required', 'string'],
            'legal_type'                => ['required', 'string'],
            'country_type'              => ['required', 'string'],
            'sourcer'                   => ['required', 'string'],
            'note'                      => ['required', 'string', 'min:3'],
            'companyIds'                => ['required', 'array'],
        ];
        $request->validate($rules);
    }

    public function destroy(array $ids, $reasonId,$request)
    {

        return $this->destroyItems(
            Supplier::class,
            $ids,
            'reason_supplier',
            $reasonId,
            'supplier_id',
            null,
            $this->getRelations(),
            'supplier',
            $request
        );
    }

    public function changeSupplierStatus(Request $request)
    {
        return $this->changeStatus($request,  Supplier::class, 'suppliers', 'reason_supplier', 'supplier_id', $this->getRelations(), 'supplier');
    }
    // store validation
    private function validateSupplier($request)
    {
        $response = ['response' => '', 'success' => false];
        $validator = Validator::make($request->all(), $this->storeRules());
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $response['success'] = true;
        }
        return $response;
    }

    // check the uniqueness of columns and its related values in database
    public function checkUniquenesOfCulumnsWithIndex(Request $request): JsonResponse
    {
        $result = [];
        $columns = $request->all();
        foreach ($columns as $column) {
            $column = collect($column);
            $isExists = Supplier::where($column->get("name"), $column->get("value"))->exists();
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

    // check for uniqueness columns and values is already exists in database or not
    public function checkUniqueness(Request $request): JsonResponse
    {
        $columns                        = $request->all();
        $result                         = [];

        foreach ($columns as $column) {

            $column                     = collect($column);
            $columnName                 = $column->get("columnName");
            $columnValue                = $column->get("value");
            $isExists = Supplier::query()->where($columnName, $columnValue)->exists();

            $result[$columnName] = $isExists;
        }

        return response()->json($result);
    }
    /**
     * this function to filter supplier
     *
     * @param [type] $query
     * @param [type] $request
     *
     */
    public function filterSupplier($query, $request)
    {



        // request values
        $created_date = $request->created_date;
        $updated_date = $request->updated_date;
        $supplier_name = $request->supplier_name;
        $supplier_trading_name = $request->supplier_trading_name;
        $commercial_type = $request->commercial_type;
        $prepration_period = $request->prepration_period;
        $supply_type = $request->supply_type;
        $legal_type = $request->legal_type;
        $country_type = $request->country_type;
        $supplier_ids = $request->supplier_ids;
        $include = $request->include;


        if ($commercial_type != null) {
            $query = $query->where('commercial_type', $commercial_type);
        }
        if ($prepration_period != null) {
            $query = $query->where('prepration_period', $prepration_period);
        }
        if ($supply_type != null) {
            $query = $query->where('supply_type',  $supply_type);
        }
        if ($legal_type != null) {
            $query = $query->where('legal_type',  $legal_type);
        }
        if ($country_type != null) {
            $query = $query->where('country_type', $country_type);
        }
        if ($created_date != null) {
            if (count($created_date) == 2) {
                $start_date = $created_date[0];
                $end_date = $created_date[1];
            } else {
                $start_date = $created_date[0];
                $end_date = null;
            }
            if ($start_date != null) {
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date != null) {
                $end_date_format = Carbon::createFromFormat('Y-m-d', $end_date);
            }
            if ($start_date == null) {
                $year = $end_date_format->format('Y');
                $month = $end_date_format->format('m');
                $day = '01';
                $start_date = $year . '-' . $month . '-' . $day;
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date) {
                if ($end_date_format->gt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($start_date_format, $end_date_format));
                } else if ($end_date_format->lessThan($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'), array($end_date_format->subDay(), $start_date_format));
                } else {
                    $query = $query->whereDate('created_at', $start_date_format);
                }
            } else {
                $query = $query->whereDate('created_at', $start_date_format);
            }
        }
        if ($updated_date != null) {
            if (count($updated_date) == 2) {
                $start_date = $updated_date[0];
                $end_date = $updated_date[1];
            } else {
                $start_date = $updated_date[0];
                $end_date = null;
            }
            if ($start_date != null) {
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date != null) {
                $end_date_format = Carbon::createFromFormat('Y-m-d', $end_date);
            }
            if ($start_date == null) {
                $year = $end_date_format->format('Y');
                $month = $end_date_format->format('m');
                $day = '01';
                $start_date = $year . '-' . $month . '-' . $day;
                $start_date_format = Carbon::createFromFormat('Y-m-d', $start_date);
            }
            if ($end_date) {
                if ($end_date_format->gt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(updated_at)'), array($start_date_format->subDay(), $end_date_format));
                } else if ($end_date_format->lt($start_date_format)) {
                    $query = $query->whereBetween(DB::raw('DATE(updated_at)'), array($end_date_format->subDay(), $start_date_format));
                } else {
                    $query = $query->whereDate('updated_at', $start_date_format);
                }
            } else {
                $query = $query->whereDate('updated_at', $start_date_format);
            }
        }
        if ($supplier_ids != null) {
            if (count($supplier_ids) > 0) {
                if ($include != null) {
                    if ($include == 0) {
                        $query = $query->whereIn('code', $supplier_ids);
                    } else if ($include == 1) {
                        $query = $query->whereNotIn('code', $supplier_ids);
                    }
                }
            }
        }
        if ($supplier_name != null) {
            $query = $query->where('supplier_name', 'LIKE', '%' . $supplier_name . '%');
        }
        if ($supplier_trading_name != null) {
            $query = $query->where('supplier_trading_name', 'LIKE', '%' . $supplier_trading_name . '%');
        }

        return $query;
    }
    public function searchSuppliers($query, $request)
    {
        $type = $request->type;
        $content = $request->contentData;
        if ($content != null) {
            if ($type == 0) {
                $query = $query->where(function ($query) use ($content) {
                    return $query->where('supplier_name', 'ILIKE', '%' . $content . '%')
                        ->orWhere('supplier_trading_name', 'ILIKE', '%' . $content . '%')
                        ->orWhere('email', 'ILIKE', '%' . $content . '%')
                        ->orWhere('main_phone', 'ILIKE', '%' . $content . '%')
                        ->orWhere('purchase_order_phone', 'ILIKE', '%' . $content . '%')
                        ->orWhere('website', 'ILIKE', '%' . $content . '%')
                        ->orWhere('prepration_period', 'ILIKE', '%' . $content . '%')
                        ->orWhere('supply_type', 'ILIKE', '%' . $content . '%')
                        ->orWhere('commercial_type', 'ILIKE', '%' . $content . '%')
                        ->orWhere('legal_type', 'ILIKE', '%' . $content . '%')
                        ->orWhere('country_type', 'ILIKE', '%' . $content . '%');
                });
            }
        }
        return $query;
    }

    public function search($request)
    {
        $type = $request->type;
        $content = $request->content;
        $queryBuilder = new UriQueryBuilder(new Supplier(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();
        $queryBuilder->query = $this->filterSupplier($queryBuilder->query, $request);
        if ($content != null) {
            if ($type == 1) {
                $companyData = $queryBuilder->query->where('code', last($content))->with($this->getRelations())->first();
                if ($companyData) {
                    return response()->json($companyData, 200);
                }
            }
        }
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function storeLocation(Request $request){
        try {
            if ($request->has('location') && $request->input('location')) {
                foreach ($request->input('location')  as $key => $location) {
                    $locationDtata = [
                        'country_id' => $location['country_id'],
                        'state_id' => $location['state_id'],
                        'location_type' => $location['location_type'],
                        'address' => $location['address'],
                        'map_link' => $location['map_link'],
                        'location_phone' => $location['location_phone'],
                        'crowd_status' => $location['crowd_status'],
                        'contact_staff_name' => $location['contact_staff_name'],
                        'notes' => $location['notes']??null,
                        'supplier_id' => $request->supplier_id,
                    ];
                    Location::create($locationDtata);
                }
            }
            return response()->json(true,Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function updateLocations(Request $request)
    {
        // $data = [];
        $supplierlocation = Location::find($request->id);
        $supplierlocation->update([
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'location_type' => $request->location_type,
            'map_link' => $request->map_link,
            'location_phone' => $request->location_phone,
            'crowd_status' => $request->crowd_status,
            'contact_staff_name' => $request->contact_staff_name,
            'address' => $request->address,
        ]);
        return response()->json($supplierlocation->load(['supplier:id,supplier_name', 'country:id,name,iso2,iso3', 'state:id,name']), 200);
    }

    public function updateSupplierBankAccount(Request $request)
    {
        // $data = [];
        $supplierBankAccount = SupplierBankAccount::find($request->id);
        $bankAccountData = $supplierBankAccount->update([
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_number_iban' => $request->bank_account_number_iban,
            'swift_code' => $request->swift_code,
            'address' => $request->address,
        ]);
        return response()->json($bankAccountData, Response::HTTP_CREATED);
    }
    public function getSupplierBankAccount($id)
    {
        $supplierbankaccount = SupplierBankAccount::with('supplier:id,supplier_name')->where('supplier_id', $id)->orderBy("id","desc")->get();
        return $supplierbankaccount;
    }
    public function getSupplierCountry()
    {
        return Country::orderBy('name','asc')->get();
    }
    public function images(Request $request)
    {
        $imagePath = "";
        try {
            if ($request->file_path) {
                $this->deleteFile($request->file_path);
                return response()->json(['result' => true]);
            }
            $request->validate($this->imagesRules());
            $this->prefix = '/product-management/pr-products/images';
            $image = $request->file('image');
            $imagePath = $this->storeFile($image);
            return response()->json(['result' => true, 'path' => $imagePath]);
        } catch (Exception $exception) {
            if ($imagePath) {
                $this->deleteFile($imagePath);
            }
        }
    }
    public function imagesRules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg'],
        ];
    }
    public function storeBankAccountRules()
    {
        return [
            'bank_name' => 'required|array',
            'bank_account_name' => 'required|array',
            'bank_account_number' => 'required|array',
            'bank_account_number_iban' => 'required|array',
            'swift_code' => 'required|array',
            'address' => 'required|array',
        ];
    }
    public function storeSupplierBankAccount(Request $request)
    {
        $data = [];
        if ($request->has('bank') && $request->input('bank')) {
            foreach ($request->input('bank')  as $key => $bankAccountData) {
                $bankAccountData = [
                    'bank_name' => $bankAccountData['bank_name'],
                    'bank_account_name' => $bankAccountData['bank_account_name'],
                    'bank_account_number' => $bankAccountData['bank_account_number'],
                    'bank_account_number_iban' => $bankAccountData['bank_account_number_iban'],
                    'swift_code' => $bankAccountData['swift_code'],
                    'address' => $bankAccountData['address'],
                    'supplier_id' => $request->supplier_id,
                ];
                $data[$key] = SupplierBankAccount::create($bankAccountData);
            }
        }
        return response()->json($data, Response::HTTP_CREATED);
    }

}
