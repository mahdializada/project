<?php
namespace App\Repositories\SingleSales;

use App\Models\CountryLanguage;
use Exception;
use App\Models\Reason;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use \App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductStudy;
use App\Repositories\QueryBuilder\UriQueryBuilder;

class ProductStudyRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new ProductStudy(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchingFields = [
            'code',
            'whereHasOne,countryLangauge.language,name',
            'study_reason',
            'status'
        ];
        if ($request->order) {
            $queryBuilder->query = $queryBuilder->query
                ->whereHas("requestAssinUser", function ($userQuery) {
                    $userQuery->where("user_id", auth()->id());
                });
        }

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchingFields);
        $statusQuery  = clone  $queryBuilder->query;
        $customColumn = ['assigned, whereHas, requestAssinUser', 'not assigned, whereDoesntHave, requestAssinUser'];
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $request->key, $customColumn);

        $designRequest = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'activeTotal, status, active',
            'inactiveTotal, status, inactive',
            'assignedTotal, whereHas, requestAssinUser',
            'notAssignedTotal, whereDoesntHave, requestAssinUser',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        return response()->json($designRequest);
    }


    public function show(Request $request)
    {
        if ($request->query->has('code')) {
            $query =  ProductStudy::with(['countryLangauge.language:id,name', "product:id,name"])
                ->where('code', $request->code);
            if ($request->order) {
                $query = $query->whereHas("requestAssinUser", function ($userQuery) {
                    $userQuery->where("user_id", auth()->id());
                });
            }
            if ($request->key == "removed") {
                $query = $query->withTrashed();
            }
            $sourcer = $query->first();
            if ($sourcer) {
                return response()->json($sourcer, Response::HTTP_OK);
            } else {
                return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
            }
        }
        if ($request->query->has('product_id')) {
            // $product_study = ProductStudy::where('status', 'active')->get();
            $product_study = ProductStudy::where('product_id', $request->product_id)->where('status', 'active')->get();
            return response()->json($product_study, Response::HTTP_OK);
        }
        return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
    }
    public function filterData($order = null, $type)
    {
        $query = ProductStudy::withTrashed();
        if ($order) {
            $query = $query->whereHas("requestAssinUser", function ($userQuery) {
                $userQuery->where("user_id", auth()->id());
            });
        }
        if ($type == "products") {
            $productIds = $query->select("product_id")->get()->pluck("product_id")->toArray();
            $products = Product::whereIn("id", $productIds)->select(["id", "name", "code"])->get();
            return response()->json($products, Response::HTTP_OK);
        } else {
            $productLanguageIds = $query->select("study_language_id")->get()->pluck("study_language_id")->toArray();
            $languages = Language::whereIn("id", $productLanguageIds)->select(["id", "name", "native"])->get();
            return response()->json($languages, Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        try {
            $productStudy = new ProductStudy();
            $attributes         = $request->only($productStudy->getFillable());
            $attributes['code'] = rand(100000, 9999999999);
            $attributes['study_language_id'] = $request->study_language_id;
            $productStudy =    ProductStudy::create($attributes);
            return response()->json(['result' => true, 'data' => $productStudy->load($this->getRelations())], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function update(Request $request)
    {

        try {
            DB::beginTransaction();
            $product_study = $request->all();
            $model = ProductStudy::findOrFail($request->get('id'));
            $model->update($product_study);
            $updated_items = $model->load($this->getRelations());
            DB::commit();
            return response()->json($updated_items);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
    public function changeProductStudyStatus(Request $request)
    {
        try {
            DB::beginTransaction();

            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');
            $statusForRestore   = $request->input('status');

            foreach ($itemIds as $id) {
                $item           = ProductStudy::withTrashed()->find($id);
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
    public function assign(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $arr = [];
            $product_study = ProductStudy::with($this->getRelations())->whereIn('id', $data['assign_ids'])->get();
            foreach ($product_study as $req) {
                foreach ($data['user_ids'] as $user_id) {
                    $arr[$user_id] = [
                        'user_id' => $request->user()->id,
                        'product_study_id' => $req->id
                    ];
                }
                $req->requestAssinUser()->syncWithoutDetaching($arr);
            }
            DB::commit();
            return $this->successResponse(null, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function unAssign(Request $request)
    {
        try {
            DB::beginTransaction();
            $orderIds = $request->input("orderIds");
            DB::table('product_study_assigned_users_ssp')->whereIn("product_study_id", $orderIds)->delete();
            DB::commit();
            return $this->successResponse(null, Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }
    public function getRelations(){
        return [
            'language:id,name',
             'requestAssinUser',
        ];
    }
    public function destroy($ids, $reason_ids)
    {
        try {
            //code...
            DB::beginTransaction();
            $proStudies = ProductStudy::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($proStudies as $proStudy) {
                if (!$proStudy->trashed()) {
                    foreach ($reasons as $reason) {
                        $proStudy->reasons()->save($reason);
                    }
                    $proStudy->reasons()->syncWithoutDetaching($reasons);
                    $proStudy->delete();
                } else {
                    $proStudy->reasons()->detach();
                    $proStudy->forceDelete();
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
}
