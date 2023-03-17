<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_names', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("columns");
            $table->string("page_name");
            $table->integer("scope_type");
            $table->boolean("default")->default(false);
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('view_names');
    }
}
