<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetCostColumnToSourcingRequestsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sourcing_requests_ssp', function (Blueprint $table) {
              $table->double('target_cost',10,2)->before('created_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sourcing_requests_ssp', function (Blueprint $table) {
            //
        });
    }
}
