<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zalo_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('app_id')->unique();
            $table->string('app_secret')->unique();
            $table->string('refresh_token')->unique();
            $table->string('access_token')->unique();
            $table->string('oa_secret_key_webhook')->nullable();
            $table->string('admin_group_id')->nullable();
            $table->timestamp('access_token_expires_at')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zalo_configs');
    }
};
