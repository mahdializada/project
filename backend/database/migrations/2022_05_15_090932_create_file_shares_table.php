<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_shares', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuidMorphs("shareable");
            $table->foreignUuid('shared_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('shared_to')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->enum("share_type", ["unlimited", "limited"]);
            $table->json("date_range")->nullable();
            $table->string("permission"); // read & write
            $table->boolean('seen')->default(false);
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
        Schema::dropIfExists('file_shares');
    }
}
