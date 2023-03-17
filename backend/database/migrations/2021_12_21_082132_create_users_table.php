<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("firstname");
            $table->string("lastname");
            $table->string("username")->unique();
            $table->string("phone")->unique();
            $table->string("whatsapp")->unique();
            $table->enum("status", User::getTypes())->default("pending");

            $table->string("email")->unique();
            $table->string("password");

            $table->enum("gender", ["male", "female"]);
            $table->date("birth_date");
            $table->boolean("tracing_status")->default(false);
            $table->string("address");
            $table->boolean("change_password");

            $table->enum("schedule_type", ["unlimited", "limited"]);
            $table->json("date_range")->nullable();
            $table->json("time_range")->nullable();

            $table->string("image");
            $table->text("note");
            $table->json("selected_companies")->nullable();
            $table->json('coords')->nullable();
            $table->boolean("functional")->default(false);


            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('language_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('country_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('state_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('current_country_id')->nullable()->constrained("countries")
                ->cascadeOnUpdate()->nullOnDelete();

            $table->uuid("translated_language_id")->nullable();
            $table->foreign("translated_language_id")->references("id")->on("translated_languages")->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('users');
    }
}
