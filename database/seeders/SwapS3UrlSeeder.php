<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SwapS3UrlSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=SwapS3UrlSeeder
     */
    public function run(): void
{
    $records1to6 = DB::table('my_ai_image_templates')->whereIn('id', [1, 2, 3, 4, 5, 6])->get();
    $records57to62 = DB::table('my_ai_image_templates')->whereIn('id', [57, 58, 59, 60, 61, 62])->get();

    if ($records1to6->count() === $records57to62->count()) {
        for ($i = 0; $i < $records1to6->count(); $i++) {
            $record1 = $records1to6[$i];
            $record2 = $records57to62[$i];

            // Hoán đổi s3_url giữa hai bản ghi
            DB::table('my_ai_image_templates')
                ->where('id', $record1->id)
                ->update(['s3_url' => $record2->s3_url]);

            DB::table('my_ai_image_templates')
                ->where('id', $record2->id)
                ->update(['s3_url' => $record1->s3_url]);
        }
    } else {
        throw new \Exception("Số lượng bản ghi giữa hai phạm vi không khớp!");
    }
}

}
