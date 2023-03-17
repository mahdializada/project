<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionTemplatesUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connection_templates_users', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("connection_template_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("user_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connection_templates_users');
    }
}
