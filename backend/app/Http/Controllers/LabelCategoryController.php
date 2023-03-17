<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabelCategory;
use Illuminate\Http\Response;

class LabelCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = LabelCategory::get();
        return $category;
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
        $rules = [
            "id"            => ['required', 'exists:label_categories,id'],
            'title'         => ['required', 'string', 'min:5', 'max:255'],
        ];
        $request->validate($rules);
        $data = LabelCategory::where('id', $id)->update(['title' => $request->input('title')]);
        $categories = LabelCategory::get();
        return response()->json(["result" => true, "data" => $data, 'categories' => $categories], Response::HTTP_OK);
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
