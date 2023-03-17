<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReasonCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_company', function (Blueprint $table) {
            $table->id();
            $table->enum("status", Company::getTypes());


            //FOREIGN KEY CONSTRAINTS
            $table->uuid('reason_id')->nullable();
            $table->foreign('reason_id')->references('id')->on('reasons')->onDelete('cascade');
            // $table->foreignUuid('reason_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate()->nullable();
            $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('reason_company');
    }
}
