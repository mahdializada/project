<?php

use App\Models\SingleSales\Ipa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpaSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipa_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignUuid('ispp_id')->constrained('ispp_ssp')->cascadeOnDelete()->cascadeOnDelete();
            $table->enum('ad_policy_violation',Ipa::getPolicyViolation()); 
            $table->enum('work_priority',Ipa::getWorkPriority());
            $table->timestamp('publication_start_period')->nullable();
            $table->timestamp('publication_end_period')->nullable();
            $table->boolean('intensify_result_confirmation');
            $table->time('start_hour')->nullable();
            $table->time('end_hour')->nullable();
            $table->integer('progressive');
            $table->enum('status',Ipa::getStatus() );
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
        Schema::dropIfExists('ipa_ssp');
    }
}
