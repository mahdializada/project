<?php

namespace App\Http\Controllers;

use ActivityLog;
use App\Models\CountryLanguage;
use App\Models\Language;
use App\Models\TranslatedLanguage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\TranslatedLangugeRepository;

class TranslatedLanguageController extends Controller
{

    private $repository;
    private $ActivityLog;

    public function __construct()
    {
        $this->repository = new TranslatedLangugeRepository();
        $this->ActivityLog = new ActivityLog();

        // $this->middleware('scope:lnv')->only(['index', 'show']);
        $this->middleware('scope:lnc')->only(['store']);
        // $this->middleware('scope:lnu')->only(['update', 'changeLanguageStatus']);
        // $this->middleware('scope:lnd')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }
    public function searchLanguage(Request $request)
    {
        return $this->repository->search($request);
    }

    public function store(Request $request)
    {
        $request->validate($this->repository->storeRules());

        // $content = TranslatedLanguage::withTrashed()->with('countryLanguage.language:id,name')->findOrFail($request->selectedLangaugeId);
        // $content = TranslatedLanguage::withTrashed()->with('countryLanguage.language:id,name')->findOrFail($request->selectedLangaugeId);
        // $this->ActivityLog->setLog($request, 'masters', 'language', 'insert');
        return $this->repository->store($request);
    }

    public function show(Request $request, $id): JsonResponse
    {
        return $this->repository->show($request, $id);
    }

    public function update(Request $request): JsonResponse
    {
        $this->repository->updateRules($request);
        return $this->repository->update($request);
    }

    public function destroy(Request $request, $ids)
    {

        // $this->ActivityLog->setLog($request, 'masters', 'language', 'delete');
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'));
    }

    public function changeLanguageStatus(Request $request)
    {

        // $this->ActivityLog->setLog($request, 'masters', 'language', 'change status');
        return $this->repository->changeLanguageStatus($request);
    }
}
