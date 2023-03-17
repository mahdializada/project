<?php

use App\Models\Country;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReasonCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_country', function (Blueprint $table) {
            $table->id();
            $table->enum("status", Country::getTypes());


            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('reason_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('country_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('reason_country');
    }
}
