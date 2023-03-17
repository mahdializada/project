<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProfileHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_profile_histories', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->string('changed_value');
            $table->string('profit_type')->nullable();

            $table->foreignUuid('profile_id')->constrained('product_profile_infos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('product_profile_histories');
    }
}
