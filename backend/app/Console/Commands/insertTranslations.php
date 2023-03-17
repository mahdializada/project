<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Phrase;
use App\Models\Country;
use App\Models\Language;
use App\Models\LanguagePhrase;
use Illuminate\Console\Command;
use App\Models\TranslatedLanguage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class insertTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:translations';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('phrases')->truncate();
        DB::table('language_phrases')->truncate();
        DB::table('translated_languages')->truncate();
        Schema::enableForeignKeyConstraints();
        $user = User::first();
        $langFind = Language::where(['name' => 'English'])->first();
        $countryFind = Country::where(['name' => 'United States'])->first();
        $country_language = DB::table('country_language')->where(['country_id' => $countryFind->id, 'language_id' => $langFind->id])->first();
        $lang = TranslatedLanguage::create(['country_language_id' => $country_language->id, 'created_by' => $user->id, 'updated_by' => $user->id, 'status' => 'active', 'direction' => 'ltr', 'code' => rand(100000, 9999999999)]);

        $phraseFile = json_decode(File::get(resource_path() . "/json/phrasesEnTranslations.json"));
        foreach ($phraseFile as $key => $phrase) {
            $phraseModel = new Phrase();
            $phrase = (array)$phrase;
            $phraseCreated = $phraseModel->create(['phrase' => $key, 'word_type' => $phrase['type'], 'code' => rand(100000, 9999999999)]);
            LanguagePhrase::create(
                [
                    'phrase_id' => $phraseCreated->id,
                    'translated_language_id' => $lang->id,
                    'translation' => $phrase['word'],  'created_by' => $user->id, 'updated_by' => $user->id, 'code' => rand(100000, 9999999999)
                ]
            );

            $this->info($phrase['word'] . ' Successfully Inerted!');
        }
        $users =  User::all();
        foreach ($users as $user) {
            $user->update(['translated_language_id' => $lang->id]);
            $user->save();
        }
        return 0;
    }
}
