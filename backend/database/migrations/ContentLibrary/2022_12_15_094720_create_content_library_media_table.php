<?php

use App\Models\ContentLibrary\ContentLibraryMedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentLibraryMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_library_media', function (Blueprint $table) {
            $table->id();
            $table->string('project_url');
            $table->string('media_size');
            $table->enum('status', ContentLibraryMedia::getTypes())->default('not publish');
            $table->foreignUuid('content_library_id')->constrained()->cascadeOnUpdate()->nullable()->cascadeOnDelete();
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
        Schema::dropIfExists('content_library_media');
    }
}
