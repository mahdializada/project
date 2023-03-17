<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('reply');
            $table->foreignUuid("comment_id")->references("id")->on("pdm_comments");
            $table->foreignUuid("user_id")->references("id")->on("users");
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
        Schema::dropIfExists('pdm_replies');
    }
}
