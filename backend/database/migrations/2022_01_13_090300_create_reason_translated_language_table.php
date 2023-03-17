<?php

use App\Models\TranslatedLanguage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReasonTranslatedLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_translated_language', function (Blueprint $table) {
            $table->id();
            $table->enum("status", TranslatedLanguage::getTypes());
            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('reason_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('translated_language_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('reason_translated_language');
    }
}
