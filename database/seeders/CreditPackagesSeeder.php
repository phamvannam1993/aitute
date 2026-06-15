<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditPackagesSeeder extends Seeder
{
    /**
     *  php artisan db:seed --class=CreditPackagesSeeder
     */
    public function run()
    {
        DB::table('credit_packages')->delete();

        $data = [
            [
                'name' => '999K Credit',
                'duration' => '3 ngày',
                'content' => '<div style="display: flex;flex-direction: column;justify-content: center; height: 100%;"><p style="text-align: center;"><span style="font-size: 22px; margin:30px 0 30px 0; font-weight: bold;">Sử dụng tất cả các tính năng</span></p> <p style="text-align: center;"><span style="font-size: 22px; margin:30px 0 30px 0; font-weight: bold;">Trong 3 ngày</span></p></div>',
                'price' => 300000,
                'link' => 'https://www.google.com/',
            ],
            [
                'name' => '4.999K Credit',
                'duration' => '12 tháng',
                'content' => '<ul><li>Các trợ lý giải quyết mọi công việc</li><li>Tạo hình ảnh từ văn bản</li><li>Hình ảnh phối cảnh</li><li>Đổi khuôn mặt</li><li>Tạo hình ảnh thành video</li><li>Tạo hình ảnh từ ảnh gốc</li><li>Chat bot</li><li>Giọng nói</li></ul>',
                'price' => 300000,
                'link' => 'https://www.google.com/',
            ],
            [
                'name' => '9.999K Credit',
                'duration' => '12 tháng',
                'content' => '<ul><li>Tạo Flim từ văn bản</li><li>Tạo Video từ ảnh và vănn bản</li><li>Làm MC ảo</li><li>Tạo âm nhạc từ văn bản</li><li>Tạo bài hát từ văn bản</li><li>Tạo slide</li><li>Các trợ lý giải quyết mọi công việc</li><li>Tạo hình ảnh từ văn bản</li><li>Hình ảnh phối cảnh</li><li>Đổi khuôn mặt</li><li>Tạo hình ảnh thành video</li><li>Tạo hình ảnh từ ảnh gốc</li><li>Chat bot</li><li>Giọng nói</li></ul>',
                'price' => 500000,
                'link' => 'https://www.google.com/',
            ],
            [
                'name' => '1.000.000 Credit',
                'duration' => '12 tháng',
                'content' => '<p style="text-align: center;margin-bottom: 30px;"><span style="font-size: 26px; margin:30px 0 30px 0; font-weight: bold;">​Sử dụng full tính năng gói 4.999 và 9.999</span></p><ul><li>Thiết kế baner, poster</li><li>Tôi là A.I &quot;Phiên bản AI của bạn&quot;</li><li>Tất cả các tính năng mới nhất được cập nhật hàng ngày</li></ul>',
                'price' => 25000000,
                'link' => 'https://www.google.com/',
            ],
        ];

        DB::table('credit_packages')->insert($data);
    }
}
