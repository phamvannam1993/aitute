<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgentsTruncateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt ràng buộc khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('agents')->truncate();   // Sau đó xóa bảng cha
        // Bật lại ràng buộc khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
