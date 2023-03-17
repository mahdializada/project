<?php


namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\DesignRequest;
use Illuminate\Http\Response;

use App\Events\SendNotification;
use Illuminate\Http\JsonResponse;
use App\Models\DesignRequestOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DesignRequestOrderUser;
use App\Models\Notification;
use App\Models\SubAction;
use App\Models\User;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class DesignRequestOrderRepository extends Repository
{

    public function getLoggedInUserPermissions()
    {
        //1. attach the permission of relevant user to the response,
        $permissions = SubAction::whereHas('actions', function (Builder $builder) {
            $builder->whereHas('subSystems', function (Builder $builder) {
                $builder->whereHas('actionSubSystems', function (Builder $builder) {
                    $builder->whereHas('users', function (Builder $builder) {
                        $builder->where('users.id', auth()->user()->id);
                    });
                });
            });
        })
            ->get()
            ->pluck('action');
        return $permissions;
    }

    public function paginate(Request $request, $onlyAssigned): JsonResponse
    {
        $key = $request->query->get("key");

        $queryBuilder = new UriQueryBuilder(new DesignRequest(), $request);
        $queryBuilder->setRelations($this->getRelations());
        $queryBuilder->setWithTrashed();
        if ($key != "all") {
            $queryBuilder->query->where("status", $key);
        }
        $queryBuilder->query->whereHas('designRequestOrder', function ($query) {
            $query->whereHas('designRequestOrderUser', function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('id', Auth::id());
                });
            });
        });
        $searchInColumns        = ['product_code', 'whereHasOne,company,name', 'whereHasOne,template,name', 'product_name', 'type', 'priority', 'status', 'storyboard_reject_count', 'design_reject_count', 'waiting_end_date', 'completed_date', 'updated_at', 'created_at'];

        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query->orderBy("created_at", 'desc');
        $designRequest   = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'instorybordTotal, status, in storyboard',
            'storybordreviewTotal, status, storyboard review',
            'indesignTotal, status, in design',
            'designReviewTotal, status, design review',
            'inprogrammingTotal, status, in programming',
        ];
        $designRequest = $this->getStatusCount($statusQuery, $designRequest, $extraData);
        // $query, $data, $extraData,
        return response()->json($designRequest);
    }


    //return only product code and product name
    public function getProductCode()
    {
        $products = DesignRequest::select(['product_code', "product_name"])->groupBy("product_code")->get();
        $templates = Template::select(["id", "name", "code"])->get();
        $data = [
            "products" => $products,
            "templates" => $templates,
        ];
        return response()->json($data);
    }



    private function getRelations(): array
    {

        return [
            'designRequestOrder',
            'template',
            "designRequestOrder.designRequestOrderUser.user:id,firstname,lastname,image",
            "company:id,name",
            "user:id,firstname,lastname",
            'statusTimes',
            'label:id,label'

        ];
    }





    public function updatePrograss(Request $request)
    {
        $user = $request->user();
        $queryBuilder = new UriQueryBuilder(new DesignRequestOrderUser(), $request);
        $prograss = $queryBuilder->query->findOrFail($request->id);
        $prograss->update(['task_prograss' => $request->task_prograss]);

        // send notification
        $notification = Notification::where('title', 'user_task_progress')->first();
        $task = DesignRequestOrder::select(['id', 'design_request_id'])->where('id', $prograss->design_request_order_id)->with('designRequest:id,code,product_name,product_code')->first();

        $design_request = $prograss->load(['designRequestOrder:id,design_request_id', 'designRequestOrder.designRequest:id,code']);

        broadcast(new SendNotification(
            $prograss->created_by,
            $notification->id,
            [
                "{$user->firstname} {$user->lastname}"
            ],
            [
                $user->firstname . ' ' . $user->lastname,
                $request->task_prograss . '%',
                "{$task->designRequest->product_name} ({$task->designRequest->product_code})"
            ],
            [
                'system' => 'Design Request',
                'design_request_id' => $design_request->designRequestOrder->designRequest->code
            ]
        ));
        return $this->successResponse($prograss);
    }
    public function search($request): JsonResponse
    {
        $queryBuilder = new UriQueryBuilder(new DesignRequest(), $request);
        $queryBuilder->query->whereHas('designRequestOrder', function ($query) {
            $query->whereHas('designRequestOrderUser', function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('id', Auth::id());
                });
            });
        });

        $data = $this->searchCode($queryBuilder->query, $request, $this->getRelations());
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
}
