<?php

use App\Models\Sourcer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonSourcersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_sourcers', function (Blueprint $table) {
            $table->id();
            $table->enum("status", Sourcer::getTypes());
            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('reason_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('sourcer_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('reason_sourcers');
    }
}
