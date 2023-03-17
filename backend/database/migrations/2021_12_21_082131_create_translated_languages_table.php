<?php

use App\Models\TranslatedLanguage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslatedLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translated_languages', function (Blueprint $table) {
            $table->uuid("id")->primary();

            $table->string("code")->unique();

            $table->enum("status", TranslatedLanguage::getTypes())->default("pending");

            $table->enum("direction", ["ltr", "rtl"]);
            // 2021_12_21_083132_create_users_table
            $table->foreignId('country_language_id')->nullable()->constrained("country_language")->cascadeOnUpdate()->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translated_languages');
    }
}
