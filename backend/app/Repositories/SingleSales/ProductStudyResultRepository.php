<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use Illuminate\Http\Request;
use App\Models\SingleSales\ProductStudyResult;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

/**
 * Class ProductStudyResultRepository.
 */
class ProductStudyResultRepository extends Repository
{
    private $sProduct;
    public function __construct(ProductStudyResult $product)
    {
        $this->sProduct = $product;
    }
    public function getAll(Request $request)
    {

        $product = new ProductStudyResult();
        if ($request->has('search') and $request->get('search')) {
            $product = $product->where('code', "LIKE", '%' . $request->get('search') . '%');
        }
        $product = $product->paginate(5);
        return  $this->successResponse($product);
    }

    public function store(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'code' => 'required',
            'study_reason' => 'required',
            'study_language' => 'nullable',
            'status' => 'nullable',
        ]);

        if ($validate->fails()) {
            return $this->errorResponse($validate->errors()->all(), 'Validation error', 409);
        }

        return DB::transaction(function () use ($request) {

            $data = $this->sProduct->create([
                'code' => rand(100000, 9999999999),
                'study_reason' => $request->get('study_reason'),
                'study_language' => $request->get('study_language'),
                'status' => $request->get('status'),
            ]);

            return $this->successResponse($data);
        });
    }
    public function update(Request $request, $id)
    {

        $validate = Validator::make($request->all(), [
            'code' => 'required',
            'study_reason' => 'required',
            'study_language' => 'nullable',
            'status' => 'nullable',
        ]);

        if ($validate->fails()) {
            return $this->errorResponse($validate->errors()->all(), 'Validation error', 409);
        }


        return DB::transaction(function () use ($request, $id) {
            $product = $this->sProduct->find($id);

            $data = $this->sProduct->update([
                'code' => rand(100000, 9999999999),
                'study_reason' => $request->get('study_reason'),
                'study_language' => $request->get('study_language'),
                'status' => $request->get('status'),
            ]);

            return $this->successResponse($data);
        });
    }

    public function destroy($ids, $reason_ids)
    {

        try {
            //code...
            DB::beginTransaction();
            $productStudyResults = ProductStudyResult::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();

            // ADD REASON
            foreach ($productStudyResults as $productStudyResult) {
                if (!$productStudyResult->trashed()) {
                    foreach ($reasons as $reason) {
                        $productStudyResult->reasons()->save($reason);
                    }
                    $productStudyResult->reasons()->syncWithoutDetaching($reasons);
                    $productStudyResult->delete();
                } else {
                    $productStudyResult->reasons()->detach();
                    $productStudyResult->forceDelete();
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
