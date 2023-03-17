<?php

namespace App\Repositories\ProductManagement;

use App\Models\ProductManagement\Attachment;
use Exception;
use App\Models\Reason;
use App\Traits\FileTrait;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use App\Models\ProductManagement\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\File;


/**
 * Class BrandRepository.
 */
class BrandRepository extends Repository
{
    use FileTrait;

    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Brand(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchingColumns = [
            'whereHasOne, country, name',
            'status',
            'name'
        ];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchingColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $brand         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'activeTotal,status,active',
            'inactiveTotal,status,inactive',
        ];
        $brand = $this->getStatusCount($statusQuery, $brand, $extraData);
        return response()->json($brand);
    }

    public function filterBrands(array $columns)
    {
        $brands = Brand::select($columns)->where('status', 'active')->get();
        return response()->json($brands);
    }

    public function store(Request $request)
    {

        try {
            DB::beginTransaction();
            $brands = $request->brands;
            $path = 'brands';
            $data = [];
            if ($request->hasFile('logos')) {
                $files = $request->file('logos');
                foreach ($files as $index => $file) {
                    $new['created_by'] = auth()->user()->id;
                    $new['code'] = rand(100000, 9999999999);
                    $new['name'] = $brands[$index];
                    $attrModel = Brand::create($new);
                    $name = $file->extension();
                    $uploadedPath = $file->store($path);
                    $attrModel->attachment()->create(['path' => $uploadedPath, 'file_type' => $name]);
                    array_push($data,$attrModel->load($this->getRelations()));
                }
            }
            DB::commit();
            // return $attrModel;
            return response()->json(['result' => true, 'brand' => $data], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 500);
        }
    }


    public function update(Request $request)
    {
        // return $request;
        try {
            DB::beginTransaction();
            $brands = $request->brands;
            if ($request->hasFile('logos')) {
                $attach = Brand::with('attachment')->findOrFail($request->id);
                $files = $request->file('logos')[0];
                    $new['name'] = $brands[0];
                    $attach->update($new);
                    $this->deleteFileAnyPath($attach->attachment->getRawOriginal("path"),'brands');
                    $name = $files->extension();
                    $attach->attachment()->update(['path' => $files->store('brands'), 'file_type' => $name]);
            }
            DB::commit();
            // return $attrModel;
            return response()->json(['result' => true, 'brand' => $attach->refresh()], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 500);
        }

    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $brands = Brand::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();

            // ADD REASON
            foreach ($brands as $brand) {
                if (!$brand->trashed()) {
                    foreach ($reasons as $reason) {
                        $brand->reasons()->save($reason);
                    }
                    $brand->reasons()->syncWithoutDetaching($reasons);
                    $brand->delete();
                } else {
                    $this->deleteFile($brand->getRawOriginal('logo'));
                    $brand->reasons()->detach();
                    $brand->forceDelete();
                }
            }
            DB::commit();

            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $th;
        }
    }


    public function storeRules(): array
    {
        return [
            'brands.*'         => 'required|min:2',
            'logos.*'         => 'required',
        ];
    }

    public function updateRules(): array
    {
        return [];
    }


    private function getRelations(): array
    {

        return [
            'country:id,name',
            'attachment'
        ];
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new Brand(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data->load($this->getRelations()), 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function changeBrandStatus(Request $request)
    {
        try {
            DB::beginTransaction();

            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');
            $statusForRestore   = $request->input('status');

            foreach ($itemIds as $id) {
                $item           = Brand::withTrashed()->find($id);
                $newStatus      = $request->input('status');
                $data = [];

                if ($statusForRestore == "restore") {
                    $item->restore();
                } else {

                    if ($newStatus == "active") {
                        $data = ['status' => $newStatus];
                    } elseif ($newStatus == "inactive") {
                        $data = ['status' => $newStatus];
                    }
                    $item->update($data);
                }
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}
