<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToLabelablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labelables', function (Blueprint $table) {
            //
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('current_status', ['active', null])->nullable()->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labelables', function (Blueprint $table) {
            //
        });
    }
}
