<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignUuid('company_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->enum('working_type', ['creation', 'competation company'])->nullable();
            $table->enum('product_type', ['single product', 'group product'])->default('single product');
            $table->enum('priority', ['high', 'medium', 'low'])->nullable();
            $table->enum('product_availablity', ['high', 'medium', 'low'])->nullable();
            $table->boolean('is_seasonal')->default(false);
            $table->string('season_or_event_name')->nullable();
            $table->string('available_quantity')->nullable();
            $table->foreignId('product_source_value_id')->nullable()->constrained("product_source_values")->nullOnDelete()->cascadeOnUpdate();
            $table->text('sale_note')->nullable();
            $table->text('product_note')->nullable();
            $table->text('chanel_note')->nullable();
            $table->text('financial_note')->nullable();
            $table->string('landing_page_url')->nullable();
            $table->unsignedBigInteger('head_selling_price')->nullable();
            $table->foreignId("currency_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("display_language_id")->nullable()->constrained("languages")->nullOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['pending', 'in review', 'in preparation', 'in hold', 'completed', 'failed', 'cancelled'])->default('pending');
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
        Schema::dropIfExists('ispp_ssp');
    }
}
