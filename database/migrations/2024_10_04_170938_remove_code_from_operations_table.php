<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCodeFromOperationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Xóa index liên quan đến cột 'code'
            $table->dropIndex(['code']);
            $table->dropIndex(['user_id', 'code', 'name', 'deleted_at']);

            // Xóa cột 'code'
            $table->dropColumn('code');

            // Tạo lại index mới không bao gồm cột 'code'
            $table->index(['user_id', 'name', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Thêm lại cột 'code'
            $table->string('code');

            // Thêm lại các index cũ bao gồm cột 'code'
            $table->index('code');
            $table->index(['user_id', 'code', 'name', 'deleted_at']);
        });
    }
}
