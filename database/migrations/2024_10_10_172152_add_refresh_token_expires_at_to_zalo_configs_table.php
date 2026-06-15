<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.s
     */
    public function up(): void
    {
        Schema::table('zalo_configs', function (Blueprint $table) {
            $table->timestamp('refresh_token_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zalo_configs', function (Blueprint $table) {
            $table->dropColumn('refresh_token_expires_at');
        });
    }
};
