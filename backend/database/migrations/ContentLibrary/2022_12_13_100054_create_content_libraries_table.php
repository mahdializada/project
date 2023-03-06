<?php

use App\Models\ContentLibrary\ContentLibrary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_libraries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('item_code');
            $table->string('item_name');
            $table->string('requested_when');
            $table->string('content_source');
            $table->string('content_type');
            $table->string('content_language');
            $table->string('season');
            $table->string('risk_palicy');
            $table->string('main_or_customer');
            $table->string('info_offer');
            $table->string('content_direction');
            $table->string('voice_over');
            $table->string('music');
            $table->string('shooting');
            $table->string('people');
            $table->string('graphics');
            $table->foreignUuid('country_id')->constrained()->cascadeOnUpdate()->nullable()->cascadeOnDelete();
            $table->foreignUuid('company_id')->constrained()->cascadeOnUpdate()->nullable()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_libraries');
    }
}
