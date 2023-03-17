<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_views', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuidMorphs("viewable");
            $table->foreignUuid("user_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp("created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_views');
    }
}
