<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type');
            $table->string('mime_type');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->unsignedBigInteger('size');
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid("parent_id")->nullable()->constrained('folders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('description');
            $table->foreignUuid("company_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("sub_system_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
