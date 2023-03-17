<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->boolean('seen')->default(false);
            $table->date('seen_at')->nullable()->default(null);

            $table->json('title_args');
            $table->json('content_args');
            $table->json('meta');

            $table->uuid("sender_id")->nullable();
            $table->foreign("sender_id")->references("id")->on("users")->cascadeOnDelete()->cascadeOnUpdate();

            $table->uuid("receiver_id")->nullable();
            $table->foreign("receiver_id")->references("id")->on("users")->nullOnDelete()->cascadeOnUpdate();

            $table->foreignUuid('notification_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('user_notifications');
    }
}
