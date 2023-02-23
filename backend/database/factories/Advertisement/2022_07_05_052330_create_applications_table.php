<?php

use Illuminate\Support\Facades\Schema;
use App\Models\Advertisement\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name");
            $table->text('organization_id')->nullable();
            $table->text('developer_token')->nullable();
            $table->text('redirect_url')->nullable();
            $table->text('scope')->nullable();
            $table->text("client_id");
            $table->text("client_secret");
            $table->text("access_token")->nullable();
            $table->text("refresh_token")->nullable();
            $table->integer("expires_in")->nullable();
            $table->enum('system_status', Application::$STATUSES)->default("ACTIVE");
            $table->enum("advertisement_status", ['active', 'inactive'])->default('active');
            $table->foreignUuid('platform_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('applications');
    }
}
