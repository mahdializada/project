<?php

use App\Models\Label;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('code')->unique();
            $table->string("title");
            $table->string('label');
            $table->string("color");
            $table->json("tabs")->nullable();
            $table->string("system")->nullable();
            $table->foreignUuid('label_category_id')->constrained("label_categories")->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum("status", Label::getTypes());
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
        Schema::dropIfExists('labels');
    }
}
