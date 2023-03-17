<?php

use App\Models\SocialMedia;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_media', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name")->unique();
            $table->string("logo")->unique();
            $table->string("website")->unique();
            $table->enum("status", SocialMedia::getTypes())->default("active");
            $table->string("sample_url_account");
            $table->foreignUuid('created_by')->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('updated_by')->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('social_media');
    }
}
