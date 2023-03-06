<?php

use App\Models\Landing\Master\Product\Requirement\RequirementCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductRequirementCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_requirement_categories', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("code")->unique();
            $table->enum("status", RequirementCategory::getTypes())->default("pending");
            $table->string("icon")->nullable();
            $table->text("description")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_requirement_categories');
    }
}
