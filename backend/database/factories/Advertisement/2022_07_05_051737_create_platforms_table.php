<?php

use App\Models\Advertisement\Platform;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("platform_name");
            $table->string("platform_key");
            $table->enum('system_status', Platform::$STATUSES)->default("ACTIVE");
            $table->enum("advertisement_status", ['active', 'inactive'])->default('active');

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
        Schema::dropIfExists('platforms');
    }
}
