<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DesignRequest;

class CreateStatusTimeConsumedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_time_consumed', function (Blueprint $table) {
            $table->id();
            $table->enum("status", DesignRequest::getStatus());
            $table->foreignUuid("design_request_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->integer("storyboard_stage");
            $table->integer("design_stage");
            $table->dateTime('created_at');
            $table->dateTime('end_date')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_time_consumed');
    }
}
