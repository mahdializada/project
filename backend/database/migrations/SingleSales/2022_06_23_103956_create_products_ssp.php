<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsSsp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("code")->unique();
            $table->string('parent_sku');
            $table->string('pcode');
            $table->string('name');
            $table->string('arabic_name')->nullable();
            // brand should make new table
            $table->text('description');
            $table->text('arabic_description')->nullable();
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('cost_per_unit');
            $table->boolean('is_published')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            //foreign keys
            $table->foreignUuid('product_img')->nullable()->constrained("files")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained("categories_ssp")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained("suppliers")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained("brands_ssp")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('products_ssp');
    }
}
