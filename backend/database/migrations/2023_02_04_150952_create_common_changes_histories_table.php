<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonChangesHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_changes_histories', function (Blueprint $table) {
            $table->id();
            $table->string('value')->nullable();
            $table->string('changeable_id');
            $table->string('changeable_type');
            $table->string('column_name')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('common_changes_histories');
    }
}
