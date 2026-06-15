<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSaleContactStatus extends Seeder
{
    /**
     * php artisan db:seed --class=UserSaleContactStatus
     */
    public function run(): void
    {
        $maxId = DB::table('user_sale_contact_statuses')->max('id');

        $data = [
            [
                'id' => $maxId + 1,
                'name' => 'Chưa liên hệ',
            ],
            [
                'id' => $maxId + 2,
                'name' => 'Không liên hệ được',
            ],
            [
                'id' => $maxId + 3,
                'name' => 'Đã gọi',
            ],
            [
                'id' => $maxId + 4,
                'name' => 'Đã đặt cọc',
            ],
            [
                'id' => $maxId + 5,
                'name' => 'Đã chốt thành công',
            ]
        ];

        DB::table('user_sale_contact_statuses')->insert($data);
    }
}
