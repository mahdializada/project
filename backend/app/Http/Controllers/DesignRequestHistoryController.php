<?php

namespace App\Http\Controllers;

use App\Models\DesignRequestHistory;
use Illuminate\Http\Request;
use App\Repositories\DesignRequestHistoryRepository;

class DesignRequestHistoryController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new DesignRequestHistoryRepository();
        $this->middleware('scope:drov')->only(['index']);
    }
    public function index(Request $request)
    {
        $repository = new DesignRequestHistoryRepository();
        return $repository->paginate($request);
    }


    public function destroy(Request $request, $ids)
    {

        $ids        = explode(",", $ids);
        if ($request->tab_key == 'deleted') {
            DesignRequestHistory::whereIn("id", $ids)->forceDelete();
        } else {
            DesignRequestHistory::whereIn("id", $ids)->delete();
        }
        return response()->json(["status" => true]);
    }
    public function design_request_histories_search(Request $request)
    {
        return $this->repository->search($request);
    }
}
