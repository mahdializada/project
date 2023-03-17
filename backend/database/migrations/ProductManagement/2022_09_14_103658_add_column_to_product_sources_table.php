<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToProductSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdm_product_sources', function (Blueprint $table) {
            $table->uuid("parent_id")->nullable();
            $table->foreign("parent_id")->references("id")->on("pdm_product_sources");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdm_product_sources', function (Blueprint $table) {
            $table->dropForeign(["parent_id"]);
        });
    }
}
