<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SuperadminSeeder2 extends Seeder
{
    /**
     * php artisan db:seed --class=SuperadminSeeder2
     */
    public function run(): void
    {
        try {
            // Tạo quyền cho keys
            //Role superadmin không cần gán quyền. Tự động apply tất cả quyền
            Permission::create(['guard_name' => 'admin', 'name' => 'keys.index']);
            Permission::create(['guard_name' => 'admin', 'name' => 'keys.create']);
            Permission::create(['guard_name' => 'admin', 'name' => 'keys.edit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'keys.destroy']);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
