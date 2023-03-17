<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomizeThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customize_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->tinyInteger("global_theme")->nullable();
            $table->tinyInteger("toolbar_theme")->nullable();
            $table->tinyInteger("toolbar_style")->nullable();
            $table->tinyInteger("content_layout")->nullable();
            $table->tinyInteger("menu_theme")->nullable();
            $table->boolean("rlt")->default(false);
            $table->string("primary_color")->nullable();
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
        Schema::dropIfExists('customize_themes');
    }
}
