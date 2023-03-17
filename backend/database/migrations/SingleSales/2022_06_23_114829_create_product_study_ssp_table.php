<?php

use App\Models\SingleSales\ProductStudy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStudySspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_study_ssp', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique()->default(rand(9999999, 1000000000));
            $table->foreignUuid("company_id")->constrained('companies')->cascadeOnUpdate()->cascadeOnUpdate(); 
            // $table->string('study_area_type');
            // $table->string("record_id")->nullable();
            $table->uuidMorphs('studyable');
            $table->foreignUuid("study_purpose_id")->nullable()->constrained('study_purposes')->cascadeOnUpdate()->cascadeOnUpdate(); 
            $table->foreignUuid("study_language_id")->constrained('languages')->cascadeOnUpdate()->cascadeOnUpdate();
            $table->enum('status', ProductStudy::getTypes())->default('new study'); 
            $table->enum('work_priority', ProductStudy::getWorkPriority());
            $table->text('order_note')->nullable();
            $table->text('study_recommendations')->nullable();
            $table->string("target_gender")->nullable();
            $table->integer("start_target_age")->nullable();
            $table->integer("end_target_age")->nullable();
            $table->string("target_note_name")->nullable();
            $table->text('target_desc');
            $table->integer("progressive");
            $table->foreignUuid('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('product_study_ssp');
    }
}
