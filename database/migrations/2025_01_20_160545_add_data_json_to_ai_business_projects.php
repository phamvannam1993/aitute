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
        Schema::table('ai_business_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_business_projects', 'data_json')) {
                $table->json('data_json')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_business_projects', function (Blueprint $table) {
            if (Schema::hasColumn('ai_business_projects', 'data_json')) {
                $table->dropColumn('data_json');
            }
        });
    }
};
