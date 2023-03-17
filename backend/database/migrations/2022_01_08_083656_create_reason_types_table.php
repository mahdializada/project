<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_types', function (Blueprint $table) {
            $table->id();

            $table->string("code")->unique();

            $table->enum('type', [
                'active', 'inactive', 'blocked', 'tracing', 'live',
                "archive", "pending", "warning", "published", "storyboard rejected",
                "design rejected", "cancelled", "removed", "not assigned","rejected",
                'failed', 'in review', 'in preparation', 'in hold', 'completed', 'in sourcing',
                'found', 'not found', 'in creation', 'in testing', 'sales moving', 'sales unstable',
                'stopped', 'in process', 'not possible', 'order made', 'order received'
            ]);
            $table->foreignId('reason_sub_system_id')->nullable()->constrained('reason_sub_system')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignUuid('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('reason_types');
    }
}
