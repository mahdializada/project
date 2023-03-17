<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Database\Seeder;
use App\Models\TranslatedLanguage;

class MetaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $translated_language = TranslatedLanguage::inRandomOrder()->first();
        User::query()->update(['translated_language_id' => $translated_language->id]);
        Folder::query()->update(['created_by' => User::first()->id]);
    }
}
