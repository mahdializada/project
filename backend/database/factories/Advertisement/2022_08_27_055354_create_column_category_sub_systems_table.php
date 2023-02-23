<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnCategorySubSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_category_sub_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('sub_system_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('column_category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('column_category_sub_systems');
    }
}
