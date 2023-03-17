<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentIndustryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_industry', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid("industry_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("department_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_industry');
    }
}
