<?php

namespace App\Http\Controllers;

use App\Exports\LanguageExport;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\LanguagePhrase;
use App\Models\TranslatedLanguage;
use App\Repositories\LanguagePhraseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LanguagePhraseController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new LanguagePhraseRepository();
        $this->middleware('scope:lnv')->only(['index', 'show']);
        $this->middleware('scope:lnc')->only(['store']);
        $this->middleware('scope:lnu')->only(['update', 'changeLanguageStatus']);
        $this->middleware('scope:lnd')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        return $this->repository->paginate($request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate($this->repository->storeRules());
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
        $ids = explode(",", $ids);
        return $this->repository->destroy($ids, $request->query->get('reasonId'));
    }

    public function forSystem(Request $request)
    {
        return $this->repository->forSystem($request);
    }

    public function isUptoDate(Request $request)
    {
        return $this->repository->isUptoDate($request);
    }

    function getRelationData($id)
    {
        $langInfo = TranslatedLanguage::with([
            "countryLanguage.country:id,name,iso2",
            "countryLanguage.language:id,name,native",
        ])
            ->find($id);
        return $langInfo;
    }

    public function exportLanguageTemplate(Request $request)
    {
        $base_language_id = $request->get('base_language_id');
        $language_fetched_id = $request->get('language_fetched_id');

        $baseLang = $this->getRelationData($base_language_id);
        $transLang = $this->getRelationData($language_fetched_id);
        $baseLangName = $baseLang->countryLanguage->language->native . ' ( ' . $baseLang->countryLanguage->country->name . ' ) ';
        $translatedLang = $transLang->countryLanguage->language->native . ' ( ' . $transLang->countryLanguage->country->name . ' )';
        Excel::store(new LanguageExport($base_language_id, $language_fetched_id, $baseLangName, $translatedLang), 'export-excel-files/languages.xlsx', 'public');
        return response()->json(env("APP_URL") . Storage::url('export-excel-files/languages.xlsx'));
    }
}
