<?php

namespace Database\Seeders;

use App\Models\MyAIImage;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyAIImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=MyAIImageSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $my_ai_image = new MyAIImage();
            $my_ai_image->model_name = 'LURCINN LANH';
            $my_ai_image->trigger_word = 'LURCINN_LANH';
            $my_ai_image->model_enpoint = 'dd343b824813b0308e495daae618c880fcec753297711787cb75cff9bef78e13';
            $my_ai_image->save();
            
            $my_ai_image = new MyAIImage();
            $my_ai_image->model_name = 'LURCINN DIU';
            $my_ai_image->trigger_word = 'LURCINN_DIU';
            $my_ai_image->model_enpoint = '0e257acc77493ef9370c3eacd8ad1da8fff7064453d4a5ee394690c1e84aab40';
            $my_ai_image->save();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
