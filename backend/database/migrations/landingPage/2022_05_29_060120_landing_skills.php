<?php

use App\Models\Landing\Skill as LandingSkill;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LandingSkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_skills', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("code")->unique();
            $table->string("icon")->nullable();
            $table->enum("status", LandingSkill::getTypes())->default("pending");
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('sub_category_id')->nullable()->constrained("product_skill_sub_categories")->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_skills');
    }
}
