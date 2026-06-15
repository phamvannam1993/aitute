<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=ProductSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $product = new Product();
            $product->name = "Cơ bản";
            $product->month = 1;
            $product->credit = 10;
            $product->price = 299000;
            $product->price_actual = 299000;
            $product->discount = 0;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            
            $product = new Product();
            $product->name = "Trung bình";
            $product->month = 1;
            $product->credit = 10;
            $product->price = 599000;
            $product->price_actual = 599000;
            $product->discount = 0;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            
            $product = new Product();
            $product->name = "VIP";
            $product->month = 1;
            $product->credit = 20;
            $product->price = 995000;
            $product->price_actual = 995000;
            $product->discount = 0;
            $product->description = "";
            $product->recommend = 0;
            $product->save();

            $product = new Product();
            $product->name = "Cơ bản";
            $product->month = 6;
            $product->credit = 10;
            $product->price = 299000;
            $product->price_actual = 269100;
            $product->discount = 10;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            
            $product = new Product();
            $product->name = "Trung bình";
            $product->month = 6;
            $product->credit = 10;
            $product->price = 599000;
            $product->price_actual = 539100;
            $product->discount = 10;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            
            $product = new Product();
            $product->name = "VIP";
            $product->month = 6;
            $product->credit = 20;
            $product->price = 995000;
            $product->price_actual = 895500;
            $product->discount = 10;
            $product->description = "";
            $product->recommend = 0;
            $product->save();

            $product = new Product();
            $product->name = "Cơ bản";
            $product->month = 12;
            $product->credit = 10;
            $product->price = 299000;
            $product->price_actual = 239200;
            $product->discount = 20;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            
            $product = new Product();
            $product->name = "Trung bình";
            $product->month = 12;
            $product->credit = 10;
            $product->price = 599000;
            $product->price_actual = 479200;
            $product->discount = 20;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            
            $product = new Product();
            $product->name = "VIP";
            $product->month = 12;
            $product->credit = 20;
            $product->price = 995000;
            $product->price_actual = 796000;
            $product->discount = 20;
            $product->description = "";
            $product->recommend = 0;
            $product->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
