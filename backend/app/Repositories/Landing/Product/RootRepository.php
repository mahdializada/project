<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2022-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker
 */

namespace App\Repositories\Landing\Product;

use App\Repositories\Landing\LandingRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request as RequestFacade;
use Exception;
use Illuminate\Http\JsonResponse;

class RootRepository extends LandingRepository
{
    public function __construct($model, $filePath)
    {
        parent::__construct($model, $filePath);
    }

    public function paginate(Request $request)
    {
        $queryBuilder = new UriQueryBuilder($this->model, $request);
        $queryBuilder->setRelations($this->getRelations())->setWithTrashed();
        $searchInColumns = ['name', 'description'];
        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery = clone  $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $request->key);

        $products = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'blockedTotal, status, blocked',
            'pendingTotal, status, pending',
            'warningTotal, status, warning',
            'removedTotal'
        ];
        $products = $this->getStatusCount($statusQuery, $products, $extraData);
        return response()->json($products);
    }

    function getRelations()
    {
        return [
            "subCategories",
            "createdBy"
        ];
    }

    /**
     * Validations rules for creating new user into server
     * @return array
     */

    public function storeRules(): array
    {
        return [
            'category.*.name' => ['required', 'string', 'min:2', 'max:32'],
            'category.*.description' => ['required', 'string', 'min:2', 'max:500'],
            "category.*.icon" => ['required', 'image', 'mimes:jpeg,png,jpg'],
            "category_id" => $this->requiredIf(),
        ];
    }

    protected function requiredIf()
    {
        return Rule::requiredIf(function () {
            return empty(RequestFacade::input("category_id")) ? false : true;
        });
    }

    public function store(Request $request)
    {
        $request->validate($this->storeRules());
        $categoryModel = null;
        $attributes = [];
        try {
            DB::beginTransaction();
            $categoryModel = $this->model->find($request->category_id);
            foreach ($request->category as $category) {
                if ($category['icon']) {
                    $attributes['icon'] = $this->storeFile($category['icon']);
                }
                $attributes['name'] = $category['name'];
                $attributes['description'] = $category['description'];
                $attributes['created_by'] = auth()->user()->id;
                $attributes['code'] = rand(10000, 8999999999);;
                if (empty($categoryModel)) {
                    $this->model->create($attributes);
                } else {
                    $categoryModel->subCategories()->create($attributes);
                }
            }
            DB::commit();

            return response()->json(['result' => true, 'category' => $categoryModel], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }


    protected function updateRules()
    {
        return [
            "category_id" => $this->requiredIf(),
            "icon" => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'category.*.name' => ['required', 'string', 'min:2', 'max:32'],
            'category.*.description' => ['required', 'string', 'min:2', 'max:500'],
        ];
    }


    public function update(Request $request, $id)
    {
        $request->validate($this->updateRules());
        $categoryModel = $request->category_id ? null : $this->model->find($id);
        $attributes = [];
        try {
            DB::beginTransaction();
            if ($request->icon) {
                $attributes['icon'] = $this->storeAndRemove($request->icon, $categoryModel->getRawOriginal("icon"));
            }
            $attributes['name'] = $request->name;
            $attributes['description'] = $request->description;
            $attributes['created_by'] = auth()->user()->id;
            $attributes['code'] = rand(10000, 8999999999);
            if ($categoryModel) {
                $categoryModel->update($attributes);
            } else {
                $subCategoryModel = $this->model->subCategories()->where('id', $id)->first();
                $subCategoryModel->update($attributes);
            }
            DB::commit();

            return response()->json(['result' => true, 'category' => $categoryModel], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }  
    }

    public function search($request): JsonResponse
    {
        $categoryQueryBuilder = new UriQueryBuilder($this->model, $request);
        $data = $this->searchCode($categoryQueryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }

    public function fetchAllCategories(Request $request)
    {
        $categories = $this->model
            ->withTrashed()
            // ->where('status', $request->status)
            ->get();
        return response()->json(['categories' => $categories]);
    }

    private function statusValidations($request)
    {
        $request->validate([
            'type'              => ['required', 'string'],
            'status'            => ['string'],
            'item_ids'          => ['required'],
            'reasons'           => ['required_if:type,hasReason', 'array'],
        ]);
    }

    public function changeCategoriesStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');

            foreach ($itemIds as $id) {
                $item  = $this->model::withTrashed()->findOrFail($id);
                $newStatus = $request->input('status');
                $item->updated_at = Carbon::now();
                $item->status = $newStatus;
                $item->save();
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function changedRemovedStatus($id)
    {
        try {
            $item  = $this->model::withTrashed()->findOrFail($id);
            $item->delete();

            return $this->successResponse(null, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }
}
