<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignRequestOrderUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_request_order_user', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid("user_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("design_request_order_id")->nullable()->constrained("design_request_orders")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("created_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("updated_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->integer("task_prograss")->default(0);
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
        Schema::dropIfExists('design_request_order_user');
    }
}
