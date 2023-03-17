<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_files', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("type")->default("file");
            $table->string("extension")->nullable();
            $table->string("mime_type")->nullable();
            $table->string("path");
            $table->string("thumbnail")->nullable();
            $table->unsignedBigInteger("size")->nullable();
            $table->string("icon")->nullable();
            $table->json("dimensions")->nullable();
            $table->text('description')->nullable();
            $table->nullableUuidMorphs('relateable');
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('company_id')->nullable()->constrained("companies")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('sub_system_id')->nullable()->constrained("sub_systems")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('system_files');
    }
}
