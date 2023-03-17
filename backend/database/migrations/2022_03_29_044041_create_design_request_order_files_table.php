<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignRequestOrderFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_request_order_files', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("url")->unique();
            $table->string('type');
            $table->string('name');
            $table->unsignedDecimal('size');

            $table->foreignUuid("design_request_order_id");
            $table->foreign("design_request_order_id", "design_file_foreign_key")
                ->references("id")->on("design_request_orders")->onDelete("CASCADE")->onUpdate("CASCADE");

            $table->foreignUuid("changed_by")->nullable();
            $table->foreign("changed_by", "design_file_changed_by_key")
                ->references("id")->on("users")->onDelete("set null")->onUpdate("CASCADE");

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
        Schema::dropIfExists('design_request_order_files');
    }
}
