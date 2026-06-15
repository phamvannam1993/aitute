<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsageLimitSeeder2 extends Seeder
{
    /**
     * php artisan db:seed --class=UsageLimitSeeder2
     */
    public function run(): void
    {
        DB::table('usage_limits')->insert([
            ['assistant_type' => 'video', 'default_limit' => 1],
        ]);
    }
}
