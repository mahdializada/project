<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibraryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('library_files', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("type")->default("file");
            $table->string("extension")->nullable();
            $table->string("mime_type")->nullable();
            $table->string("path");
            $table->string("thumbnail")->nullable();
            $table->unsignedBigInteger("size")->nullable();
            $table->string("icon")->nullable();
            $table->string("password")->nullable();
            $table->boolean("read_only")->default(false);
            $table->string("status")->default("raw")->nullable();
            $table->string("upload_type")->default("system")->nullable();
            $table->json("dimensions")->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('design_request_id')->nullable()->constrained("design_requests")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid("company_id")->nullable()->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('library_files');
    }
}
