<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperadminSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=SuperadminSeeder
     */
    public function run(): void
    {
        try {
            $superadminRole = Role::create(['guard_name' => 'admin', 'name' => 'superadmin']);
            $adminRole = Role::create(['guard_name' => 'admin', 'name' => 'admin']);

            Permission::create(['guard_name' => 'admin', 'name' => 'users.index']);
            Permission::create(['guard_name' => 'admin', 'name' => 'users.create']);
            Permission::create(['guard_name' => 'admin', 'name' => 'users.edit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'users.destroy']);
            Permission::create(['guard_name' => 'admin', 'name' => 'users.add-credit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'activity-logs.index']);

            //Role superadmin không cần gán quyền. Tự động apply tất cả quyền

            //Có thể gán quyền cho Role admin
            // $adminRole->givePermissionTo([
            //     'users.index',
            //     'users.create',
            //     'users.edit',
            //     'users.destroy',
            //     'users.add-credit',
            //     'activity-logs.index',
            // ]);

            $superadmin = new Admin;
            $superadmin->email = 'superadmin@gotechjsc.com';
            $superadmin->password = bcrypt('g0TOvCNZ69');
            $superadmin->save();

            $superadmin->assignRole($superadminRole);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
