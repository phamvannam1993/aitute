<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MessageHistories;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First update existing values to match our boolean expectations
        DB::statement("UPDATE message_histories SET status = '1' WHERE status = 'nóng'");
        DB::statement("UPDATE message_histories SET status = '0' WHERE status = 'lạnh'");
        
        // Now change the column type to boolean
        Schema::table('message_histories', function (Blueprint $table) {
            $table->boolean('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First change back to string
        Schema::table('message_histories', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });
        
        // Then convert boolean values back to their string representations
        DB::statement("UPDATE message_histories SET status = 'nóng' WHERE status = '1'");
        DB::statement("UPDATE message_histories SET status = 'lạnh' WHERE status = '0'");
    }
};