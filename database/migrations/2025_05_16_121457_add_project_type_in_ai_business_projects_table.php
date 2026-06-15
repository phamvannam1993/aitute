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
            $table->string('project_type')->nullable();
            $table->integer('total_campaign_days')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_business_projects', function (Blueprint $table) {
            $table->dropColumn('project_type');
            $table->dropColumn('total_campaign_days');
        });
    }
};
