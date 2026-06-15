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
        Schema::table('zalo_configs', function ($table) {
            $table->dropUnique('zalo_configs_access_token_unique');
            $table->dropUnique('zalo_configs_refresh_token_unique');
            $table->text('refresh_token')->change();
            $table->text('access_token')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zalo_configs', function (Blueprint $table) {
            $table->string('refresh_token')->unique()->change();
            $table->string('access_token')->unique()->change();
        });
    }
};
