<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=UserTypeSeeder
     */
    public function run(): void
    {
        $userType = new UserType;
        $userType->type_name = 'Giáo Viên';
        $userType->save();

        $userType = new UserType;
        $userType->type_name = 'Học sinh';
        $userType->save();
    }
}
