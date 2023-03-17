<?php

use App\Models\RequestStorage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_storages', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string('amount')->nullable();
            $table->string('size')->nullable();
            $table->enum('type', ['limited', 'unlimited']);
            $table->enum("status", RequestStorage::getTypes())->default("pending");
            $table->foreignUuid("user_id")->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("approved_by")->nullable()->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('request_storages');
    }
}
