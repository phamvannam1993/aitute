<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SuperadminSeeder3 extends Seeder
{
    /**
     * php artisan db:seed --class=SuperadminSeeder3
     */
    public function run(): void
    {
        try {
            // Tạo quyền thêm user
            //Role superadmin không cần gán quyền. Tự động apply tất cả quyền
            Permission::create(['guard_name' => 'admin', 'name' => 'users.export']);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
