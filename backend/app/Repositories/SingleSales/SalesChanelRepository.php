<?php

namespace App\Repositories\SingleSales;

use App\Models\SingleSales\SalesChanel;
use App\Repositories\Repository;
use Illuminate\Http\Request;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Models\Reason;

class SalesChanelRepository extends Repository{
    public function paginate(Request $request){
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new SalesChanel(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $searchInColumns = [
            'name',
        ];

        $queryBuilder->query = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        //      s
        $statusQuery = clone $queryBuilder->query;
        $queryBuilder->query = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $query = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
        ];
        $query = $this->getStatusCount($statusQuery, $query, $extraData);
        return response()->json($query);
    }


    public function Store(Request $request)
    {
        $result = DB::transaction(function() use($request){
            return SalesChanel::create([
                'name'=>$request->get('name')
            ]);
        });

        return $this->successResponse($result->load($this->getRelations()), Response::HTTP_CREATED);
    }

    public function update(Request $request)
    {
        $result = DB::transaction(function() use($request){
            $salesCh = SalesChanel::find($request->id);
            $salesCh->update(['name'=>$request->get("name")]);
        });
        return $this->successResponse($result->load($this->getRelations()), Response::HTTP_CREATED);
    }


    public function show(Request $request)
    {
        if ($request->query->has('code')) {
            $query =  SalesChanel::with($this->getRelations())
                ->where('code', $request->code);
            if ($request->key == "removed") {
                $query = $query->withTrashed();
            }
            $query = $query->first();
            if ($query) {
                return response()->json($query, Response::HTTP_OK);
            } else {
                return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
            }
        }
        return response()->json(["message" => "not found"], Response::HTTP_NOT_FOUND);
    }


    public function storeRules():array{
        return ['name'=>["required"]];
    }

    public function updateRules():array
    {
        return ['name'=>["required"]];
    }

    public function destroy($ids, $reason_ids)
    {
        try {
            //code...
            DB::beginTransaction();
            $query = SalesChanel::withTrashed()->whereIn('id', $ids)->get();
            $reasons = Reason::whereIn('id', $reason_ids)->get();
            

            // ADD REASON
            foreach ($query as $q) {
                if (!$q->trashed()) {
                    foreach ($reasons as $reason) {
                        $q->reasons()->save($reason);
                    }
                    $q->reasons()->syncWithoutDetaching($reasons);
                    DB::table('quantity_reservation_products')->where("quantity_reservation_id",$q->id)->delete();
                    $q->delete();
                } else {
                    $q->reasons()->detach();
                    $q->forceDelete();
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

    public function getRelations(){
        return ['parent,id,name'];
    }
}