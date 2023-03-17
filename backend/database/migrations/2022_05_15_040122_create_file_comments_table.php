<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_comments', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuidMorphs("commentable");
            $table->text('comment', 300);
            $table->foreignUuid("user_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::table('file_comments', function (Blueprint $table) {
            $table->foreignUuid("comment_id")->nullable()->constrained("file_comments")->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_comments');
    }
}
