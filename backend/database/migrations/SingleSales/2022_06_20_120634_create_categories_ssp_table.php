<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_ssp', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->string('arabic_name')->nullable();
            $table->string('icon');
            $table->enum('status',['active','inactive','removed'])->default('inactive');
            $table->string('description')->nullable();
            $table->string('arabic_description')->nullable();
            $table->enum('type',['product study','interest']);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('categories_ssp', function (Blueprint $table) {
            $table->foreignUuid("parent_id")->nullable()->constrained('categories_ssp')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories_ssp');
    }
}
