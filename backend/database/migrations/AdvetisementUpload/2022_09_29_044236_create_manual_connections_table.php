<?php

use App\Models\Advertisement\Connection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_connections', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("hashed_code")->nullable();
            $table->string("pcode");
            $table->string("pname")->nullable();
            $table->text("generated_link")->nullable();
            $table->text("landing_page_link")->nullable();
            $table->string("media_link")->nullable();
            $table->string('media_id')->nullable();
            $table->string('media_size')->nullable();
            $table->enum("sales_type", Connection::$salesType);
            $table->string("server_account_id")->nullable()->index();
            $table->string("server_campaign_id")->nullable()->index();
            $table->string("server_ad_adset_id")->nullable()->index();
            $table->string("server_ad_id")->nullable()->index();
            $table->enum('system_status', Connection::$STATUSES)->default("ACTIVE");
            $table->foreignUuid('country_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('company_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('platform_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('application_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('ad_account_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('history_ad_id')->nullable()->constrained('manual_upload_ads')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('ispp_id')->constrained("ispp_ssp")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('manual_connections');
    }
}
