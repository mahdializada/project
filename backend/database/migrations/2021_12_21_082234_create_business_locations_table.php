<?php

use App\Models\BusinessLocation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_locations', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name")->unique();
            $table->string("email")->unique();
            $table->enum("status", BusinessLocation::getTypes())->default("pending");

            $table->enum(
                "location_type",
                ["head office", "service branch", "branch", "none physical location"]
            )->default("branch");

            $table->text("map_link")->nullable();
            $table->string("address");
            $table->string("note");



            $table->foreignUuid('country_id')->nullable()->constrained("countries")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('state_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('business_locations');
    }
}
