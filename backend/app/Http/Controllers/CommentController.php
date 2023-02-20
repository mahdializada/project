<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    $comment = Comment::with('user','replies');
     // returning data for search
     if($request->type == 'search'){
        $searchValue = $request->searchData;
        if($request->searchTab == "All" && $request->searchData == null){
            $data = $comment;
        }
        elseif ($request->searchTab != "All") {

            if ($request->searchData ==  null) {
                $data = $comment->where('is_active', $request->searchTab);

            } else {

                $data = $comment->where('is_active', $request->searchTab)->where( function (Builder $query) use ($searchValue){
                    $query->where('comment', 'like', '%' . $searchValue . '%')->orWhere('id', 'like', $searchValue);
                });
            }
        }
        else{
            $data = $comment->where('comment', 'like', '%' . $request->searchData . '%')->orWhere('id', 'like', $request->searchData);
        }
        $result = $data->get();
        return  response()->json($result);
    }
    // returning data according to tap
    if ($request->tabkey && $request->tabkey != 'all') {
        if($request->tabkey == 1){
            $data = $comment->where('is_active', 1);
            return  $data->get();
        }else{
            $data = $comment->where('is_active', 0);
            return  $data->get();
        }
    }else{
        return $comment->get();
    }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id){
            $find = Comment::find($request->id);
            $update_comment = $find->update([
            'comment'=>$request->comment,
            'user_id'=>$request->user_id,
        ]);
        return response()->json($update_comment,200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $comment=Comment::find($id);
        $change= $comment->update(['is_active' => !$comment->is_active ]);
         return response()->json($change ,200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $del = Comment::whereIn('id',$request->ids)->get();
        Comment::whereIn('id',$request->ids)->delete();
    }
}
