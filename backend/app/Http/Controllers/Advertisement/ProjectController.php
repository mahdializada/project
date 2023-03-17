<?php


namespace App\Http\Controllers\Advertisement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\Project;
use App\Repositories\Advertisement\ProjectRepository;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('scope:advprojectsv')->only(['index', 'show']);
        $this->middleware('scope:advprojectsc')->only(['store']);
        $this->middleware('scope:advprojectsu')->only(['update']);
        $this->middleware('scope:advprojectsd')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $repository = new ProjectRepository();
        return $repository->paginate($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $repository = new ProjectRepository();
        $request->validate($repository->validationRule());
        return $repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advertisement\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $repository = new ProjectRepository();
        return $repository->show($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advertisement\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $repository = new ProjectRepository();
        if ($request->restore) {
            $request->validate([
                "ids" => ["required", "array",],
                "ids.*" => ["uuid", "exists:projects,id"],
            ]);
            return $repository->restoreProjects($request->ids);
        }
        $request->validate($repository->validationRule(true));
        return $repository->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advertisement\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy($ids)
    {
        $ids        = explode(",", $ids);
        $repository = new ProjectRepository();
        return $repository->destroy($ids);
    }


    public function getProjects()
    {
        return Project::get(['id','name']);
    }
}
