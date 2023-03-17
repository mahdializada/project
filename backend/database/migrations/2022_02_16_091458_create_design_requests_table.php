<?php

use App\Models\DesignRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_requests', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("product_code");
            $table->string("product_name");
            $table->enum("type", DesignRequest::getTypes())->nullable();
            $table->enum("priority", DesignRequest::getPriority())->default("medium");
            $table->enum("status", DesignRequest::getStatus())->default("waiting");
            $table->foreignUuid("created_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("updated_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("company_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("label_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->enum("prev_status", DesignRequest::getStatus());
            $table->integer("storyboard_reject_count")->default(0);
            $table->boolean("in_storyboard")->default(false);
            $table->integer("design_reject_count")->default(0);
            $table->dateTime("waiting_end_date")->nullable()->default(null);
            $table->dateTime("completed_date")->nullable()->default(null);
            $table->foreignUuid("template_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement("alter table design_requests add code integer not null unique AUTO_INCREMENT;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('design_requests');
    }
}
