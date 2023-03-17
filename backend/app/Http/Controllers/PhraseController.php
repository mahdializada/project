<?php

namespace App\Http\Controllers;

use App\Models\Phrase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PhraseController extends Controller
{

    private $repository;

    public function index(Request $request): JsonResponse
    {
        return response()->json(Phrase::all());
    }


    // public function store(Request $request): JsonResponse
    // {
    //     $request->validate($this->repository->storeRules());
    //     return $this->repository->store($request);
    // }

    // public function show(Request $request, $id): JsonResponse
    // {
    //     return $this->repository->show($request, $id);
    // }

    // public function update(Request $request): JsonResponse
    // {
    //     $this->repository->updateRules($request);
    //     return $this->repository->update($request);
    // }

    // public function destroy(Request $request, $ids): JsonResponse
    // {
    //     $ids = explode(",", $ids);
    //     return $this->repository->destroy($ids, $request->query->get('reasonId'));
    // }

    // public function changeLanguageStatus(Request $request): JsonResponse
    // {
    //     return $this->repository->changeLanguageStatus($request);
    // }
}
