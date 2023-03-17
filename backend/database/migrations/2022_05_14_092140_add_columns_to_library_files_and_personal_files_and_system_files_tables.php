<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToLibraryFilesAndPersonalFilesAndSystemFilesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('library_files', function (Blueprint $table) {
            $table->foreignUuid("parent_id")->nullable()->constrained('library_files')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('personal_files', function (Blueprint $table) {
            $table->foreignUuid("parent_id")->nullable()->constrained('personal_files')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('system_files', function (Blueprint $table) {
            $table->foreignUuid("parent_id")->nullable()->constrained('system_files')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('library_files', function (Blueprint $table) {
            $table->dropForeign(["parent_id"]);
        });

        Schema::table('personal_files', function (Blueprint $table) {
            $table->dropForeign(["parent_id"]);
        });

        Schema::table('system_files', function (Blueprint $table) {
            $table->dropForeign(["parent_id"]);
        });
    }
}
