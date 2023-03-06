<?php

use App\Models\Landing\Master\Product\Skill\SkillCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductSkillCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skill_categories', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("code")->unique();
            $table->string("icon")->nullable();
            $table->enum("status", SkillCategory::getTypes())->default("pending");
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
        Schema::dropIfExists('product_skill_categories');
    }
}
