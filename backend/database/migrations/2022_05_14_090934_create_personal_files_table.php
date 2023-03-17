<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_files', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid('user_id')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->string("name");
            $table->string("type")->default("file");
            $table->string("extension")->nullable();
            $table->string("mime_type")->nullable();
            $table->string("path");
            $table->string("thumbnail")->nullable();
            $table->unsignedBigInteger("size")->nullable();
            $table->string("icon")->nullable();
            $table->string("password")->nullable();
            $table->json("dimensions")->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('sharedBy_id')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('personal_files');
    }
}
