<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReasonUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_user', function (Blueprint $table) {

            $table->id();
            $table->enum("status", User::getTypes());

            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('reason_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('reason_user');
    }
}
