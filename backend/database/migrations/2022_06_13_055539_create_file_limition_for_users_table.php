<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileLimitionForUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_limition_for_users', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('limited_size')->nullable();
            $table->string("used_size")->default("0")->nullable();
            $table->string("documents_used")->nullable()->default("0");
            $table->string("videos_used")->nullable()->default("0");
            $table->string("images_used")->nullable()->default("0");
            $table->string("audios_used")->nullable()->default("0");
            $table->string("share_files_used")->nullable()->default("0");
            $table->string("default_view")->default("grid");
            $table->string("default_sorting")->default("name");
            $table->foreignUuid("user_id")->nullable()->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('file_limition_for_users');
    }
}
