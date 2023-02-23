<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_rate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_from')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete();;
            $table->foreignId('currency_to')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete();;
            $table->double('rate');
            $table->enum("status", ["active", "pending", 'inactive', 'deleted']);
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->softDeletes();
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
        Schema::dropIfExists('daily_rate');
    }
}
