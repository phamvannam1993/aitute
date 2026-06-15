<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCodeFromOccupationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('occupations', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['user_id', 'code', 'name', 'deleted_at']);

            if (Schema::hasColumn('occupations', 'code')) {
                $table->dropColumn('code');
            }

            // Tạo lại index mới không có cột 'code'
            $table->index(['user_id', 'name', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occupations', function (Blueprint $table) {
            $table->string('code');
            $table->index('code');
            $table->index(['user_id', 'code', 'name', 'deleted_at']);
        });
    }
}
