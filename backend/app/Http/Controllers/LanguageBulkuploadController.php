<?php

namespace App\Http\Controllers;

use App\Models\LanguagePhrase;
use App\Models\Phrase;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\returnArgument;

class LanguageBulkuploadController extends Controller
{


    
    public function __construct()
    {

        $this->middleware('scope:lnc')->only(['store']);
        //log middlware
        // $this->middleware('dailylogs:users,user,insert')->only(['store']);
        // $this->middleware('dailylogs:users,user,update')->only(['update']);

    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $translatatedlanguages = $request->get('langs');
            $lang_id = $request->get('lang_id');
            LanguagePhrase::where('translated_language_id', $lang_id)->delete();
            foreach ($translatatedlanguages as $lang) {
                if(!(empty($lang["translated_value"]."") || is_null($lang["translated_value"].""))){
                    $phraseId = $this->findPharase($lang['phrase']);
                    $attributes['translation'] = $lang["translated_value"]."";
                    $attributes['code'] = rand(1000000, 100000000000);
                    $attributes['created_by'] = $request->user()->id;
                    $attributes['updated_by'] = $request->user()->id;
                    $attributes['translated_language_id'] = $lang_id;
                    $attributes['phrase_id'] = $phraseId;
                    LanguagePhrase::create($attributes);
                }
            }
            DB::commit(); 
            return response()->json(['message' => 'Translated languages have been successfully inserted']);
        } catch (Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    function findPharase($phrase){
        return $phrase = Phrase::wherePhrase($phrase)->first()->id;
    }
}
