<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name")->unique();
            $table->json("domain")->nullable();
            $table->enum("status", Company::getTypes())->default("pending");
            $table->enum("advertisement_status", ['active', 'inactive'])->default('active');

            $table->string("logo")->nullable();
            $table->string("email")->unique()->nullable();
            $table->string("investment_type");
            $table->text("note");

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
        Schema::dropIfExists('companies');
    }
}
