<?php

namespace App\Repositories;

use App\Repositories\QueryBuilder\UriQueryBuilder;
use App\Models\SocialMedia;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\FileTrait;

class SocialMediaRepository extends Repository
{

    use FileTrait;

    private $filePath = "master-management/social";

    public function index($request)
    {

    }
    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get('key');
        return $this->getSocialMedia($request, $key);
    }

    private function  getSocialMedia($request, $key): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new SocialMedia(), $request);
        $queryBuilder->setWithTrashed();

        $searchInColumns    = ['name', 'status', 'website', 'sample_url_account', 'updated_at', 'created_at'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $socialMediaData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'removedTotal'
        ];
        $socialMediaData = $this->getStatusCount($statusQuery, $socialMediaData, $extraData);
        return response()->json($socialMediaData);
    }



    // return a specific project if found else return not found
    public function show($id): JsonResponse
    {
        $socialMedia = SocialMedia::find($id);
        if ($socialMedia) {
            return $this->successResponse($socialMedia);
        }
        return $this->errorResponse("Not Found", Response::HTTP_NOT_FOUND);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $socialMedia = new SocialMedia();
            $attributes = $request->only($socialMedia->getFillable());
            $this->prefix = "social-media";
            $imagePath = $this->storeFile($request->file('logo'));
            $attributes["logo"] = $imagePath;
            $attributes['code'] = rand(100000, 999999999);
            $attributes['created_by'] = $request->user()->id;
            $attributes['updated_by'] = $request->user()->id;
            $socialMedia            = SocialMedia::create($attributes);
            DB::commit();
            return $this->successResponse($socialMedia, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'logo' => ['required', 'image'],
            'website' => 'required|url',
        ];
    }

    public function update(Request $request): JsonResponse
    {

        $logoPath = null;
        try {
            DB::beginTransaction();

            $mediaData         = SocialMedia::findOrFail($request->input("id"));
            $attributes      = $request->only($mediaData->getFillable());
            $attributes['updated_by'] = $request->user()->id;
            if ($request->hasFile("logo") && is_file($request->file("logo"))) {
                $this->prefix = $this->filePath;
                $logoPath = $this->storeAndRemove($request->file("logo"), $mediaData->getRawOriginal('logo'));
                $attributes["logo"] = $logoPath;
            } else {
                unset($attributes["logo"]);
            }
            $mediaData->update($attributes);
            DB::commit();
            return $this->successResponse($mediaData, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            if ($logoPath) {
                $this->deleteFile($logoPath);
            }
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function updateRules(Request $request)
    {
        $id                                         = $request->get("id");
        $validationRules                                = $this->storeRules();
        $validationRules['name'] = ['required', 'string', 'min:2', 'max:255', "unique:social_media,name," . $id];
        $imageValidations = ["required"];
        if ($request->hasFile("logo") && is_file($request->file("logo"))) {
            $imageValidations[] = "image";
        }

        $validationRules["logo"] = $imageValidations;
        $validationRules['website'] = ['required', 'url'];
        $request->validate($validationRules);
    }


    public function changeSocialMediaStatus(Request $request)
    {
        return $this->changeStatus($request, SocialMedia::class, 'social_media', 'reason_social_media', 'social_media_id');
    }


    public function destroy(array $ids, $reasonId)
    {
        // return $this->destroyItems(SocialMedia::class, $ids, 'reason_social_media', $reasonId, 'social_media_id', "logo");
    }

    public function search($request)
    {
        $queryBuilder = new UriQueryBuilder(new SocialMedia(), $request);
        $data = $this->searchCode($queryBuilder->query, $request);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
