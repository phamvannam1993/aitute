<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsageLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usage_limits')->insert([
            ['assistant_type' => 'text', 'default_limit' => 1],
            ['assistant_type' => 'image', 'default_limit' => 1],
            ['assistant_type' => 'audio', 'default_limit' => 1],
            ['assistant_type' => 'mc', 'default_limit' => 1],
        ]);
    }
}
