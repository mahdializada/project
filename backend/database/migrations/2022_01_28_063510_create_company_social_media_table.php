<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySocialMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_social_media', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('social_media_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['company_id', 'social_media_id']);
            $table->string('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_social_media');
    }
}
