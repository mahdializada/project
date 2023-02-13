<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('user_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('change_password')->nullable();
            $table->string('phone');
            $table->string('image');
            $table->enum('gender',['male','female']);
            $table->date('birth_date');
            $table->enum('permission_type',['role','team','user']);
            $table->enum('status',['pending ','active','inactive','blocked','removed'])->default('pending');
            $table->enum('prev_status',['pending ','active','inactive','blocked','removed'])->nullable();
            
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
        Schema::dropIfExists('users');
    }
};
