<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_setting', function (Blueprint $table) {
            $table->id();
            $table->string("setting_name");
            $table->json("columns");
            $table->string("table_name");
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('column_setting');
    }
}
