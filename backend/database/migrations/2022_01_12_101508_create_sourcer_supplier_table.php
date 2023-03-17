<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcerSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sourcer_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('sourcer_id')->constrained('sourcers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('sourcer_supplier');
    }
}
