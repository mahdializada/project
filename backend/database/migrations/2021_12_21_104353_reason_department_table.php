<?php

use App\Models\Department;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReasonDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_department', function (Blueprint $table) {
            $table->id();

            $table->enum("status", Department::getTypes());


            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('reason_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('department_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('changed_by')->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('reason_department');
    }
}
