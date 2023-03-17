<?php


namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\DesignRequestHistory;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Http\Response;

class DesignRequestHistoryRepository extends Repository
{

    public function paginate(Request $request): JsonResponse
    {
        $key = $request->query->get("key");
        $historyQueryBuilder = new UriQueryBuilder(new DesignRequestHistory(), $request);
        $historyQueryBuilder->setWithTrashed();
        $historyQueryBuilder->query = $historyQueryBuilder->query->where('user_id', $request->user()->id);
        $historyQueryBuilder->setRelations($this->getRelations());
  

        if ($key == "removed") {
            $historyQueryBuilder->query =   $historyQueryBuilder->query->where("deleted_at", "!=", null);
        } else if ($key != "all") {
            $historyQueryBuilder->query =   $historyQueryBuilder->query->where("status", $key);
        }
        $searchInColumns        = ['product_code', 'whereHasOne,company,name', 'whereHasOne,template,name', 'product_name', 'type', 'priority', 'status', 'storyboard_reject_count', 'design_reject_count', 'waiting_end_date', 'completed_date', 'updated_at', 'created_at'];
        $historyQueryBuilder->query    = $this->filterRecords($historyQueryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $historyQueryBuilder->query;
        $designRequestHistory   = $historyQueryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'removedTotal',
            'waitingTotal, status, waiting',
            'instorybordTotal, status, in storyboard',
            'storybordreviewTotal, status, storyboard review',
            'storyboardRejectTotal, status, storyboard rejected',
            'indesignTotal, status, in design',
            'designReviewTotal, status, design review',
            'designRejectedTotal, status, design rejected',
            'inprogrammingTotal, status, in programming',
            'cancelledTotal, status, cancelled',
            'completedTotal, status, completed',
           
        ];
        $designRequestHistory = $this->getStatusCount($statusQuery, $designRequestHistory, $extraData);

        
         
        return response()->json($designRequestHistory);
    }
    public function search($request): JsonResponse
    {
        $historyQueryBuilder = new UriQueryBuilder(new DesignRequestHistory(), $request); 
        $data = $this->searchCode($historyQueryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
    private function getRelations(): array
    {

        return [
            'template',
            "company:id,name",
            "user:id,firstname,lastname",
            "createdBy:id,firstname,lastname,image",
            "updatedBy:id,firstname,lastname,image"
        ];
    }

    

   
    
}
