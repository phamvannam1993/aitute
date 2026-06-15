
Original file line number	Diff line number	Diff line change
@@ -0,0 +1,28 @@
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
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->boolean('is_convert_ratio')->default(false);
            $table->string('ratio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            //
        });
    }
};
