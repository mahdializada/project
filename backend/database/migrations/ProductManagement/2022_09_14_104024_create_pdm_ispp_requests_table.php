<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('working_type', ['creation', 'competition']);
            $table->enum('work_priority', ['high', 'medium', 'low']);
            $table->boolean('is_group');
            $table->enum('products_availability', ['available_in_stock', 'available_with_seller']);
            $table->bigInteger('available_quantity');
            $table->boolean('is_product_seasonal');
            $table->string('season_or_event_name');
            $table->text('individual_sale_note');
            $table->text('products_note');
            $table->json('display_languages');
            $table->text('store_channel_note');
            $table->double('head_selling_prize');
            $table->text('financial_info_note');
            $table->enum('status', ['pending', 'rejected', 'in_sale_module', 'sale_module_review', 'sale_model_reject', 'in_study', 'study_review', 'study_reject', 'in_content_creaion', 'content_creation_review', 'content_creation_review', 'completed', 'in_hold', 'canceled', 'deleted']);

            $table->foreignUuid("company_id")->references("id")->on("companies");
            $table->foreignUuid("product_source_value_id")->references("id")->on("pdm_sources_values");
            $table->foreignUuid("currency_id")->references("id")->on("pdm_currencies");
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
        Schema::dropIfExists('pdm_ispp_requests');
    }
}
