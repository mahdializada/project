<?php

namespace App\Http\Controllers\Advertisement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\Platform;
use App\Repositories\Advertisement\PlatformRepository;

class PlatformController extends Controller
{
    private PlatformRepository $repository;

    public function __construct()
    {
        $this->middleware('scope:advplatformsv')->only(['index', 'show', 'fetchPlatforms']);
        $this->middleware('scope:advplatformsc')->only(['store']);
        $this->middleware('scope:advplatformsu')->only(['update']);
        $this->middleware('scope:advplatformsd')->only(['destroy']);
        $this->repository = new PlatformRepository();
    }

    public function index(Request $request)
    {
        if ($request->query("only_platforms")) {
            return $this->fetchPlatforms();
        }
        return  $this->repository->paginate($request);
    }

    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    public function show(Request $request)
    {
        if ($request->companies) {
            return $this->repository->fetchCompanies();
        }
        return $this->repository->show($request);
    }

    public function update(Request $request)
    {
        if ($request->restore) {
            $request->validate([
                "ids" => ["required", "array",],
                "ids.*" => ["uuid", "exists:platforms,id"],
            ]);
            return  $this->repository->restorePlatforms($request->ids);
        }
        $request->validate($this->repository->updateRules());
        return $this->repository->update($request);
    }

    public function fetchPlatforms()
    {
        $columns = ["id", "platform_name"];
        $platforms = Platform::select($columns)->get();
        return response()->json(["result" => true, "platforms" => $platforms]);
    }
}
