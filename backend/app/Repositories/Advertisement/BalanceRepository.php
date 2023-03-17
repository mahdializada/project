<?php

namespace App\Repositories\Advertisement;

use App\Models\Advertisement\AdAccount;
use App\Models\Advertisement\AdAccountBalance;
use App\Models\Advertisement\DailyRate;
use App\Models\Currency;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Carbon;

/**
 * Class CategoryRepository.
 */
class BalanceRepository extends Repository
{

    public function show($id): JsonResponse
    {


        $query = AdAccountBalance::where('add_account_id', $id)
            ->with(['createdBy:id,firstname,lastname,image'])->orderBy('created_at', 'desc')->get();

        $adAccount = AdAccount::whereId($id)->select('id', 'name', 'ad_account_balance')->first();
        return response()->json(['result' => true, 'data' => $query, 'adAccount' => $adAccount]);
    }

    public function store(Request $request)
    {

        try {
            // return $request->all();
            $attribute['payment_type'] = $request->payment_type;
            $attribute['currency'] = $request->currency;
            $attribute['balance'] = $request->amount;
            $attribute['created_by'] = $request->user()->id;
            $attribute['add_account_id'] = $request->ad_account_id;
            $attribute['type'] = $request->id ? "update" : "insert";
            $data = AdAccountBalance::create($attribute);
            $adAccount = AdAccount::find($request->ad_account_id);
            if ($request->id)
                $adAccount['ad_account_balance'] = $request->amount;
            else
                $adAccount['ad_account_balance'] = $adAccount['ad_account_balance'] + $request->amount;
            $adAccount->save();
            $data->load('createdBy:id,firstname,lastname,image', 'adAccount:id,name,ad_account_balance');
            return response()->json(['result' => true, 'data' => $data, 'current_balance' => $adAccount['ad_account_balance']], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(Request $request)
    {

        $attributes = [];
        try {
            $query = AdAccountBalance::find($request->id);
            $query['payment_type'] = $request->payment_type;
            $query['currency'] = $request->currency;
            $query['balance'] = $request->amount;
            $query['created_by'] = $request->user()->id;
            $query['updated_at'] = Carbon::now();
            $query->save();

            return response()->json(['result' => true], Response::HTTP_CREATED);
        } catch (Exception $exception) {

            return $this->errorResponse($exception->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $rate = AdAccountBalance::find($id);
            $rate->delete();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {

            return response()->json($th->getMessage(), 404);
        }
    }
}
