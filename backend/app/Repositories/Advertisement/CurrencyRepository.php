<?php

namespace App\Repositories\Advertisement;

use App\Models\Advertisement\DailyRate;
use App\Models\Currency;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Repositories\QueryBuilder\UriQueryBuilder;

/**
 * Class CategoryRepository.
 */
class CurrencyRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key") ?? 'all';
        $queryBuilder = new UriQueryBuilder(new DailyRate(), $request);

        $queryBuilder->setRelations(['currencyFrom:id,name', 'currencyTo:id,name', 'creator:id,firstname,lastname']);

        $searchInColumns        = ['id', 'whereHasOne,currencyFrom,name', 'whereHasOne,currencyFrom,name', 'whereHasOne,currencyTo,name', 'whereHasOne,creator,firstname,lastname', 'created_at'];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);

        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $dailtRate         = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            "activeTotal, status, active",
            'pendingTotal, status, pending',
            'inactiveTotal, status, inactive',
            'removedTotal'
        ];
        $dailtRate = $this->getStatusCount($statusQuery, $dailtRate, $extraData);
        return response()->json($dailtRate);
    }

    public function store(Request $request)
    {
        try {

            $attribute['currency_from'] = $request->currency_from;
            $attribute['currency_to'] = $request->currency_to;
            $attribute['rate'] = $request->rate;
            $attribute['status'] = 'active';
            $attribute['created_by'] = $request->user()->id;
            $data = DailyRate::create($attribute);
            return response()->json(['result' => true, 'data' => $data], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(Request $request)
    {

        $attributes = [];
        try {
            DB::beginTransaction();

            DB::commit();
            return response()->json(['result' => true, 'platform' => []], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function destroy($ids)
    {

        try {
            //code...
            DB::beginTransaction();
            foreach ($ids as $id) {
                $rate = DailyRate::withTrashed()->find($id);

                if ($rate->status == 'deleted') {
                    $rate->forceDelete();
                } else {

                    $rate->status = 'deleted';
                    $rate->save();
                    $rate->delete();
                }
            }
            DB::commit();
            return response()->json(['result' => 123], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage(), 404);
        }
    }

    public function getAllCurrencies()
    {
        return Currency::get();
    }


    public function changeDailyRateStatus(Request $request)
    {

        try {
            DB::beginTransaction();
            $itemIds = $request->input('item_ids');

            $newStatus = $request->input('status');
            foreach ($itemIds as $id) {
                $item = DailyRate::withTrashed()->findOrFail($id);

                // if ($item->status == 'removed') {
                //     $item->restore();
                // }
                $oldRate = DailyRate::where('currency_to', $item->currency_to)->where('currency_from', $item->currency_from)->where('status', 'active')
                    ->update([
                        'status' => 'inactive',

                    ]);


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
}
