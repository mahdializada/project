<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignRequestRejectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_request_reject', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->enum("status", ["design rejected", "storyboard rejected", "cancelled", "removed", "not assigned"]);

            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('design_request_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('design_request_reject');
    }
}
