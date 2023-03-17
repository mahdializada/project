<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdAccountSubscribtionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_account_subscribtions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ad_account_id')->constrained('ad_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('subscribtion_id');
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
        Schema::dropIfExists('ad_account_subscribtions');
    }
}
