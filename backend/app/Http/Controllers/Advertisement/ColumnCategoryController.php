<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\ColumnCategory;
use App\Models\Advertisement\TabColumn;
use App\Models\SubSystem;
use App\Repositories\Advertisement\ColumnSetting\ColumnCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColumnCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $repository;
    public function __construct()
    {

        $this->repository = new ColumnCategoryRepository();
    }

    public function index(Request $request)
    {
        //
        return  $this->repository->paginate($request);
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
        return  $this->repository->store($request);
    }
    public function changeStatus(Request $request)
    {
        # code...
        return $this->repository->changeActionStatus($request);
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
    public function destroy(Request $request, $ids)
    {
        //
        $ids        = explode(",", $ids);
        return $this->repository->destroy($ids);
    }

    public function search(Request  $request)
    {
        return $this->repository->search($request);
    }
    public function fetchAllCategory(Request $request)
    {
        # code...
        return response()->json(ColumnCategory::select('id', 'category_name')->get());
    }

    public function addNewCategory(Request $request)
    {
        # code...
        try {
            //code...
            $category = $request->all();
            $category["created_by"] = Auth()->user()->id;
            $category['code'] = uniqueCode(ColumnCategory::class, '');
            $result =    ColumnCategory::create($category);
            return response()->json(["id" => $result->id, "category_name" => $result->category_name]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["result" => false, "error" => $th]);
        }
    }

    public function saveColumnChanges(Request $request)
    {
        # code...
        try {
            $column = TabColumn::findOrFail($request->id);
            $column =   $column->update($request->all());
            return response()->json(['result' => true, "data" => $column]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['result' => false, "error" => $th->getMessage()], 400);
        }
    }
    public function subsystemColumnsCategory(Request $request)
    {
        # code...
        $subsystem = explode("_", $request->sub_system);
        $phrase = $subsystem[0];

        $ids = SubSystem::where('phrase',  'LIKE', '%' . $phrase . '%')->pluck('id')->toArray();
        $categories = ColumnCategory::whereHas("subSystems", function ($query) use ($ids) {
            $query->whereIn("sub_system_id", $ids);
        })->select(['id', 'category_name'])->get();

        return response()->json($categories);
    }
}
