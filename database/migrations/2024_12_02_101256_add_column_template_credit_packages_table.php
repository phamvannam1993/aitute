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
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->string('credit')->nullable();
            $table->string('title')->nullable();
            $table->enum('template', ['Trial', 'Standard', 'Premium', 'Vip'])->default('Trial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->dropColumn(['template', 'credit', 'title']);
        });
    }
};
