<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiAssistant;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Công nghệ', 'description' => 'Công nghệ và sự đổi mới.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Y tế', 'description' => 'Dịch vụ chăm sóc sức khỏe và cải tiến y học.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Giáo dục', 'description' => 'Các tổ chức và dịch vụ giáo dục.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tài chính', 'description' => 'Ngân hàng, đầu tư và dịch vụ tài chính.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nông nghiệp', 'description' => 'Trồng trọt, chăn nuôi và các dịch vụ nông nghiệp.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Xây dựng', 'description' => 'Xây dựng và phát triển hạ tầng.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bán lẻ', 'description' => 'Bán lẻ và hàng tiêu dùng.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Du lịch', 'description' => 'Dịch vụ du lịch và lữ hành.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vận tải', 'description' => 'Dịch vụ vận chuyển và logistics.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Truyền thông', 'description' => 'Truyền thông và giải trí.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
