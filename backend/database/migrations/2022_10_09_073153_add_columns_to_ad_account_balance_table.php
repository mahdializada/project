<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAdAccountBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ad_account_balances', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('amount');
            $table->double('balance')->before('created_at');
            $table->string('currency');
            $table->string('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ad_account_balances', function (Blueprint $table) {
            //
        });
    }
}
