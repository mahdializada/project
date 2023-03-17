<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTranslatedLanguagesTable extends Migration
{
    // 2021_12_21_091435_add_column_to_users_table
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translated_languages', function (Blueprint $table) {
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->foreign("created_by")->references("id")->on("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreign("updated_by")->references("id")->on("users")->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translated_languages', function (Blueprint $table) {
            //
        });
    }
}
