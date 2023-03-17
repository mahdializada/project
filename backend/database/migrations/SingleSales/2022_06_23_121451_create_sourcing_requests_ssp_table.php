<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcingRequestsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sourcing_requests_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->enum("sourcing_type", ["quantity check (old)", "supplier check (new)"])->nullable();
            $table->foreignUuid('state_id')->nullable()->constrained('states')->nullOnDelete()->cascadeOnUpdate();
            $table->enum("required_shipping_method", ["air", "sea", "land", "all"])->nullable();
            $table->enum("status", ["pending", "rejected", "in sourcing", "in hold", "sourcing failed", "semi found", "found", "canceled", "deleted"])->nullable();
            $table->text("note")->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('source_requests_ssp');
    }
}
