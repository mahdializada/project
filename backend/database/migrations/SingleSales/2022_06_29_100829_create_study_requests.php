<?php

use Illuminate\Support\Facades\Schema;
use App\Models\SingleSales\StudyRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_requests', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique()->default(rand(9999999, 1000000000));
            $table->string("name");
            $table->text('study_reason')->nullable();
            $table->enum('status', StudyRequest::getTypes())->default('pending');
            $table->foreignUuid('study_language_id')->nullable()->constrained("languages")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained('products_ssp')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('study_requests');
    }
}
