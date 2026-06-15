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
        Schema::table('zalo_configs', function (Blueprint $table) {
            $table->boolean('is_public')->default(false);
            $table->json('user_id_by_app_for_test')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zalo_configs', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'user_id_by_app_for_test']);
        });
    }
};
