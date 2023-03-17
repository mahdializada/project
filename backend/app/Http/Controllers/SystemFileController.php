<?php

namespace App\Http\Controllers;

use App\Models\SystemFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subSystemId    = $request->input('sub_system_id');
        $companyId      = $request->input('company_id');
        if($subSystemId){
            $files = SystemFile::where([
                'company_id'    => $companyId,
                'sub_system_id' => $subSystemId,
                'type'          => 'file'
            ])->with("createdBy:id,firstname,lastname")
            ->orderBy("type", "DESC")
            ->orderBy("name", "ASC")
            ->get();
        }else{
            $files = SystemFile::where([
                'company_id'    => $companyId,
                'type'          => 'folder'
            ])->with("createdBy:id,firstname,lastname")
            ->orderBy("type", "DESC")
            ->orderBy("name", "ASC")
            ->get();
        }

        return response()->json(["files" => $files]);
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
    public function destroy($id)
    {
        //
    }
}
