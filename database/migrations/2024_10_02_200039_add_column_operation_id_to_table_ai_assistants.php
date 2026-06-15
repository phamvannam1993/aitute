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
        Schema::table('ai_assistants', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_assistants', 'occupation_id')) {
                $table->unsignedBigInteger('occupation_id')->nullable()->after('admin_id');
                $table->foreign('occupation_id')->references('id')->on('occupations')->onDelete('set null');
            }

            if (!Schema::hasColumn('ai_assistants', 'operation_id')) {
                $table->unsignedBigInteger('operation_id')->nullable()->after('occupation_id');
                $table->foreign('operation_id')->references('id')->on('operations')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            if (Schema::hasColumn('ai_assistants', 'occupation_id')) {
                $table->dropForeign(['occupation_id']);
                $table->dropColumn('occupation_id');
            }
            if (Schema::hasColumn('ai_assistants', 'operation_id')) {
                $table->dropForeign(['operation_id']);
                $table->dropColumn('operation_id');
            }
        });
    }
};
