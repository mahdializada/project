<?php

use App\Models\SingleSales\ProductStudyFormComponent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyFormComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_study_form_components', function (Blueprint $table) {
         
            $table->uuid("id")->primary();
            $table->enum('type', ProductStudyFormComponent::getType());
            $table->text('type_code');
            $table->string("label_name")->nullable();
            $table->foreignUuid("product_study_model_id")->constrained('product_study_model')->cascadeOnUpdate()->cascadeOnDelete(); 
           
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
        Schema::dropIfExists('study_form_components');
    }
}
