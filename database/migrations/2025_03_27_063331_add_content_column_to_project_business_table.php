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
            $table->text('content')->nullable()->after('description');
            $table->json('image_urls')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_business_projects', function (Blueprint $table) {
            $table->dropColumn(['image_urls', 'content']);
        });
    }
};
