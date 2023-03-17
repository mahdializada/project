<?php

use App\Models\Template;
use App\Models\DesignRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->enum("type", DesignRequest::getTypes());
            $table->foreignUuid("created_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        DB::statement("alter table templates add code integer not null unique AUTO_INCREMENT;");
        Template::create(["name" => "Fredy", 'type' => 'landing page design']);
        Template::create(["name" => "Oredo", 'type' => 'landing page design']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates');
    }
}
