<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cate= JobCategory::with('user');
        return $cate->get();
        // return "djfkasjflas";
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
            $categorEdit=JobCategory::find($request->id);
            $categorEdit->update([
                'category'=>$request->category,
                'user_id'=>$request->user_id,
            ]);
            return response()->json($categorEdit,200);
 
        }else{
            $cate=Jobcategory::create([
                'category'=>$request->category,
                'user_id'=>$request->user_id,
            ]);
            return response()->json($cate,201);
 
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $jobs = JobCategory::whereIn('id', $request)->get();
        \File::delete($jobs);
        JobCategory::whereIn('id', $request)->delete();

    }
}
