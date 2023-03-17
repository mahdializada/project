<?php

use App\Models\Supplier;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReasonSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_supplier', function (Blueprint $table) {
            $table->id();
            $table->enum("status", Supplier::getTypes());
            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('reason_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('supplier_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('reason_supplier');
    }
}
