<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateItemStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item_code');
            $table->string('country_id');
            $table->string('item_status');
            $table->string('color');
            $table->boolean('isActive')->default(true);
            $table->timestamp('end_date');
            $table->foreignUuid('created_by')->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('item_statuses');
    }
}
