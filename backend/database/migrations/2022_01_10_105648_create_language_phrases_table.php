<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagePhrasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_phrases', function (Blueprint $table) {
            $table->id();

            $table->text("translation");

            $table->string("code")->unique();

            $table->foreignId('phrase_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();

            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();

            $table->foreignUuid('translated_language_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('language_phrases');
    }
}
