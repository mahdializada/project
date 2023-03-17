<?php

use App\Models\Supplier;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("supplier_trading_name");
            $table->string("supplier_name");
            $table->string("email")->unique();
            $table->string("main_phone")->unique();
            $table->string("purchase_order_phone")->unique();
            $table->enum("status", Supplier::getTypes())->default("pending");
            $table->string('website')->nullable();
            $table->string('prepration_period');
            $table->string('supply_type');
            $table->string('commercial_type');
            $table->string('legal_type');
            $table->string('country_type');
            $table->text("note")->nullable();
            
            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('created_by')->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('updated_by')->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('suppliers');
    }
}
