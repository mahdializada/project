<?php


namespace App\Repositories;

use Exception;
use App\Models\ReasonRequestStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\RequestStorage;
use Illuminate\Http\JsonResponse;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\FileLimitionForUsers;
use App\Models\Notification;
use App\Events\SendNotification;
use App\Models\User;
use App\Events\Updated;




class RequestStorageRepository extends Repository
{
    public function paginate(Request $request): JsonResponse
    {
        $key                    = $request->query->get("key");
        $queryBuilder           = new UriQueryBuilder(new RequestStorage(), $request);
        $queryBuilder->setRelations(['user']);
        $searchInColumns        = ['status', 'type',  'amount', 'size','updated_at', 'created_at', 'whereHasOne,user,id' ];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $requestStorageData = $queryBuilder->build()->paginate()->getData();
        $extraData = [
            'allTotal',
            'approvedTotal, status, approved',
            'rejectedTotal, status, rejected',
            'pendingTotal, status, pending'
        ];
        $requestStorageData = $this->getStatusCount($statusQuery, $requestStorageData, $extraData, false);
        return response()->json($requestStorageData);
    }

    public function show($id): JsonResponse
    {
        $storageRequest = RequestStorage::with(['user'])->findOrFail($id);
        $user_id = $storageRequest->user_id;

        $companies = User::find($user_id)->companies;
        $qeury = FileLimitionForUsers::where('user_id', $user_id)->latest()->first();

        $limitedSize = $qeury->limited_size;
        $freeSize = (int) $qeury->limited_size - (int) $qeury->used_size;
        $storageRequest["current_size"] =  (string) $limitedSize;
        $storageRequest["used_size"] =  $qeury->used_size;
        $storageRequest["free_size"] = (string) $freeSize;
        $storageRequest["companies"] = $companies;

        return response()->json($storageRequest);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $LimitQurey = FileLimitionForUsers::where('user_id', $user->id)->first()->limited_size;
        if ($LimitQurey == "unlimited") {
            return response()->json(['message' => "you have unlimited size file storage"]);
        } else {
            $query  = RequestStorage::where("user_id",  $user->id)->latest()->first();
            if ($query && $query->status == "pending") {
                return response()->json(['message' => "pending"]);
            } else {
                RequestStorage::create([
                    "user_id"   => $request->user()->id,
                    "code"      => rand(10000, 8999999999),
                    "size"      => $request->type === 'limited' ? $request->size : null,
                    "amount"    => $request->type === 'limited' ? $request->amount : null,
                    "type"      => $request->type,
                ]);
                $item  =RequestStorage::with('user')->where("user_id",  $user->id)->where("status",'pending')->latest()->first();

                broadcast(new Updated('request-storage', $item, 'created'));
                return response()->json(['result' => true, 'message' => "your request has been successfuly sent."]);
            }
        }
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

    public function changeRequestStorageStatus(Request $request)
    {
        $this->statusValidations($request);
        try {
            DB::beginTransaction();
            $type               = $request->input('type');
            $itemIds            = $request->input('item_ids');
            $boradcasetData     = [];
            $notifiables        = [];

            foreach ($itemIds as $id) {
                $item           = RequestStorage::findOrFail($id);
                $newStatus      = $request->input('status');
                $currentStatus  = $item->status;

                $currentItem                  = new \stdClass;
                $currentItem->item            = $item;
                $currentItem->currentStatus   = $currentStatus;
                $currentItem->newStatus       = $newStatus;

                $boradcasetData[]             = $currentItem;
                $notify                       = new \stdClass;
                $notify->item                 = $item;
                $notify->userIds              = User::where('id', $item->user_id)->get("id")->toArray();
                $data = [];

                $notify->type = 'sample';
                if ($newStatus == "rejected") {
                    $data = ['status' => $newStatus];
                    $notify->type = 'rejected';
                } elseif ($newStatus == "approved") {
                    $data = ['status' => $newStatus, 'approved_by' =>  $request->user()->id];
                    $notify->type = 'approved';
                }

                if (in_array($notify->type, ['rejected', 'approved']))
                    $notifiables[]  = $notify;

                $item->update($data);
                if ($type   == "hasReason") {
                    foreach ($request->input('reasons') as $reason) {
                        ReasonRequestStorage::create([
                            'reason_id' => $reason,
                            'request_storage_id'  => $item->id,
                            'changed_by' =>  Auth::id(),
                        ]);
                    }
                }

                if ($item->status == 'approved') {
                    $amount =  $item->amount;
                    if ($item->type == "limited") {
                        $limitedStorage = FileLimitionForUsers::where('user_id', $item->user_id)->first()->limited_size;
                        if ($limitedStorage != "unlimited") {

                            if ($item->size == 'MB') {
                                $amount =  $amount * 1024 * 1024;
                            } elseif ($item->size == 'GB') {
                                $amount =  $amount * 1024 * 1024 * 1024;
                            }
                        }
                        $amount = $limitedStorage + $amount;
                    } elseif ($item->type == "unlimited") {
                        $amount = "unlimited";
                    }

                    FileLimitionForUsers::where('user_id', $item->user_id)->update([
                        'limited_size' =>  $amount,
                    ]);
                }
            }
            foreach ($boradcasetData as $broadcaset) {
                event(new Updated('request-storage', [
                    "item"              => $broadcaset->item->id,
                    "current_status"    => $broadcaset->currentStatus,
                    "new_status"        => $broadcaset->newStatus,
                ], 'status-changed'));
            }
            $this->sendNotification($notifiables);
            DB::commit();
            return response()->json(["result" => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }

    private function sendNotification($notifiables)
    {
        $userId = Auth::user();
        $firstname = $userId->firstname;
        $lastname = $userId->lastname;

        foreach ($notifiables as $notify) {
            $item = $notify->item;
            $notification   = Notification::where('title', $notify->type)->first();
            foreach ($notify->userIds as $user) {
                broadcast(new SendNotification(
                    $user['id'],
                    $notification->id,
                    [],
                    [$firstname . " " . $lastname, ' ' . "your_request_for" . " $item->amount ($item->size) "],
                    []
                ));
            }
        }
    }

    public function search(Request $request)
    {
        $queryBuilder = new UriQueryBuilder(new RequestStorage(), $request);
        $data = $this->searchCode($queryBuilder->query, $request, [], false);
        return $data ? response()->json($data, 200) : response()->json(['result' => false], Response::HTTP_NO_CONTENT);
    }
    public function destroy($id)
    {
        broadcast(new Updated('request-storage', $id, 'destroy'));
        $result = RequestStorage::where('id', $id)->where("status", 'pending')->delete();
        if ($result)
            return response()->json(["result" => true]);
        else
            return response()->json(["result" => false]);
    }
}
