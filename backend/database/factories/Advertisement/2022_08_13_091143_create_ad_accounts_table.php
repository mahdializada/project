<?php

use Illuminate\Support\Facades\Schema;
use App\Models\Advertisement\AdAccount;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_accounts', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name");
            $table->string("account_id")->index();
            $table->string("status")->nullable();
            $table->string("currency")->nullable();
            $table->string("timezone_name")->nullable();
            $table->string("type")->nullable();
            $table->string("balance")->nullable();
            $table->string("organization_id")->nullable();
            $table->string("billing_center_id")->nullable();
            $table->enum('system_status', AdAccount::$STATUSES)->default("ACTIVE");
            $table->double("ad_account_balance")->default(0.00);
            $table->timestamp("server_created_at")->nullable();
            $table->timestamp("server_updated_at")->nullable();
            $table->foreignUuid('application_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('ad_accounts');
    }
}
