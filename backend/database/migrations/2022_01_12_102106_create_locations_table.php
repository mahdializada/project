<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("location_type");
            $table->text("address")->nullable();
            $table->text("map_link")->nullable();
            $table->string("location_phone");
            $table->string("crowd_status");
            $table->string("contact_staff_name")->nullable();
            $table->text("notes")->nullable();
            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('supplier_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('state_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('locations');
    }
}
