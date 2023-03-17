<?php

use App\Models\Sourcer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sourcers', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name");
            $table->string("last_name");
            $table->string("phone_number");
            $table->string("email");
            $table->enum('status',Sourcer::getTypes())->default('inactive');
            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('country_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('company_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('sourcers');
    }
}
