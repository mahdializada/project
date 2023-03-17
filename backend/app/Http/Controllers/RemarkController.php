<?php

namespace App\Http\Controllers;

use App\Models\Advertisement\HistoryAd;
use App\Models\Reaction;
use App\Models\Remark;
use App\Repositories\RemarkRepository;
use Aws\History;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemarkController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new RemarkRepository();
    }
    public function index(Request $request)
    {
        return $this->repository->index($request);
    }
    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => ['required'],
            'remark' => ['required']
        ]);
        return $this->repository->update($request);
    }
    public function show(Request $request, $id)
    {
    }
    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }

    public function reactRemarks(Request $request)
    {
        $reaction =  Reaction::where(['remark_id' => $request->remark_id, 'user_id' => Auth::user()->id])->first();
        if ($reaction) {
            $data = $request->all();
            $reaction->update($data);

            return response()->json($reaction->load('user:id,firstname,lastname,image'));
        } else {
            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            $reaction =  Reaction::create($data);


            return response()->json($reaction->load('user:id,firstname,lastname,image'));
        }
        return response()->json(false);
    }
    public function deleteRemark(Request $request, $id)
    {

        $reaction =  Reaction::where(['remark_id' => $id, 'user_id' => Auth::user()->id])->delete();
        if ($reaction) {
            return response()->json(['result' => true], 200);
        } else {
            return response()->json(['result' => false], 200);
        }
    }
}
