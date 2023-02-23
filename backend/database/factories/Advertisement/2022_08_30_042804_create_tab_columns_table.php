<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tab_columns', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('value');
            $table->boolean('visible')->default(true);
            $table->boolean('sortable')->default(false);
            $table->string('tooltip')->nullable();
            $table->foreignId('tab_id')->constrained('sub_system_tabs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('column_categories')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tab_columns');
    }
}
