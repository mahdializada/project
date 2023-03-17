<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SocialMediaRepository;
use Illuminate\Http\JsonResponse;

class SocialMediaController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new SocialMediaRepository();
        $this->middleware('scope:smv')->only(['index']);
        $this->middleware('scope:smc')->only(['store']);
        $this->middleware('scope:smu')->only(['update', 'restore']);
        $this->middleware('scope:smd')->only(['destroy']);

        // log middlware
        $this->middleware('dailylogs:masters,social_media,insert')->only(['store']);
        $this->middleware('dailylogs:masters,social_media,update')->only(['update']);
        $this->middleware('dailylogs:masters,social_media,delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }

    public function searchSocialMedia(Request $request)
    {
        return $this->repository->search($request);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        return $this->repository->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        return $this->repository->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ids)
    {
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'));
    }
    public function changeSocialMediaStatus(Request $request): JsonResponse
    {
        return $this->repository->changeSocialMediaStatus($request);
    }
}
