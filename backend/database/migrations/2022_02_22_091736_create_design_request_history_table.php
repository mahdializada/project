<?php

use App\Models\DesignRequest;
use App\Models\DesignRequestOrder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignRequestHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_request_history', function (Blueprint $table) {
            $table->id();
            $table->string("product_code");
            $table->string("code");
            $table->string("product_name");
            $table->enum("type", DesignRequest::getTypes());
            $table->enum("priority", DesignRequest::getPriority())->default("medium");
            $table->enum("status", DesignRequest::getStatus())->default("waiting");
            $table->enum("prev_status", DesignRequest::getStatus());
            $table->integer("storyboard_reject_count")->default(0);
            $table->integer("design_reject_count")->default(0);
            $table->dateTime("waiting_end_date")->nullable()->default(null);
            $table->dateTime("completed_date")->nullable()->default(null);
            $table->enum("order_type", DesignRequestOrder::getOrderType())->default("scratch");
            $table->text("sales_note")->nullable();
            $table->text("storyboard_note")->nullable();
            $table->text("design_note")->nullable();
            $table->string("design_link")->nullable();
            $table->string("landing_page_link")->nullable();
            $table->integer("task_prograss")->default(0);

            $table->foreignUuid("design_request_id")->nullable()->constrained("design_requests")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("template_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("user_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("created_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("updated_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("company_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('design_request_history');
    }
}
