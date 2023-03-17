<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('product_name');
            $table->string('product_name_arabic');
            $table->string('product_code')->unique();
            $table->char('max_code',10)->nullable(); 
            $table->string('sales_type');
            $table->string("status")->default('new_sales');
            $table->text("page_link")->nullable();
            $table->foreignUuid('country_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('company_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->softDeletes();
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
        Schema::dropIfExists('online_sales');
    }
}
