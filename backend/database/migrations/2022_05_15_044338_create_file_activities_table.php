<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FileActivity;

class CreateFileActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_activities', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuidMorphs("activityable");
            $table->enum("action", FileActivity::$types);
            $table->foreignUuid("user_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('file_pinneds');
    }
}
