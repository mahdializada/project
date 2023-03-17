<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStudyResultSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_study_result_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("code")->unique();
            $table->json('features');
            $table->json('specification');
            $table->json('strength_point');
            $table->json('usages');
            $table->json('target_audience');
            $table->json('benefits');
            $table->json('weaknesses');
            $table->json('opportunities');
            $table->json('threats');
            $table->text('study_notes');
            $table->string('seller_information');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignUuid("product_study_id")->constrained('product_study_ssp')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('product_study_result_ssp');
    }
}
