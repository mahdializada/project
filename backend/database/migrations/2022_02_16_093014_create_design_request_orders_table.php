<?php

use App\Models\DesignRequestOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignRequestOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_request_orders', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->enum("order_type", DesignRequestOrder::getOrderType())->default("scratch");
            $table->text("sales_note")->nullable();
            $table->text("storyboard_note")->nullable();
            $table->text("design_note")->nullable();
            $table->string("design_link")->nullable();
            $table->string("landing_page_link")->nullable();
            $table->string("file_url")->nullable();

            $table->foreignUuid("created_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("updated_by")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("design_request_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('design_request_orders');
    }
}
