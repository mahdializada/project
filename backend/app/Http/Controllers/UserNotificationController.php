<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Repositories\QueryBuilder\UriQueryBuilder;

class UserNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $queryBuilder = new UriQueryBuilder(new UserNotification(), $request);
        $queryBuilder->setRelations([
            'notification',
            'receiver:id,code,image,firstname,lastname',
            'sender:id,code,image,firstname,lastname'
        ]);
        $queryBuilder->query = $queryBuilder->query->where('receiver_id', $user->id);
        $notifications = $queryBuilder->build()->paginate()->getData();

        $queryBuilder2 = new UriQueryBuilder(new UserNotification(), $request);
        $seenCount   = $queryBuilder2->query;
        $notifications->seenCount = $seenCount->where(['receiver_id' =>  $user->id, 'seen' => true])->count();

        $queryBuilder3 = new UriQueryBuilder(new UserNotification(), $request);
        $notSeenCount   = $queryBuilder3->query;
        $notifications->notSeenCount = $notSeenCount->where(['receiver_id' =>  $user->id, 'seen' => false])->count();

        return response()->json($notifications);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function seen(Request $request)
    {
        $user = $request->user();
        if ($request->has('all')) {
            $note = UserNotification::where(['receiver_id' => $user->id, 'seen' => false]);
            $note->update(['seen' => true]);
            return response()->json(['result' => true]);
        }
        $items = $request->only('items');
        $note = UserNotification::whereIn('id', $items);
        $note->update(['seen' => true]);
        return response()->json(['result' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function show(UserNotification $userNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserNotification $userNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserNotification $userNotification)
    {
        //
    }
}
