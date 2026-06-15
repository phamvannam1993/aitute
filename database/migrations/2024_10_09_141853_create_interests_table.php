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
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('occupation_id')->constrained()->onDelete('cascade');
            $table->foreignId('operation_id')->constrained()->onDelete('cascade');
            $table->boolean('is_interested')->default(false);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['user_id', 'operation_id', 'is_interested', 'deleted_at'], 'interest_user_op_idx');
            $table->index(['user_id', 'occupation_id', 'operation_id', 'is_interested', 'deleted_at'], 'interest_user_op_oc_idx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interests');
    }
};
