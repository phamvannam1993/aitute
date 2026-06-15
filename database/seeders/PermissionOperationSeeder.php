<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionOperationSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=PermissionOperationSeeder
     */
    public function run(): void
    {
        try {
            $operationRole = Role::create(['guard_name' => 'admin', 'name' => 'operation']);
            $operationRole->givePermissionTo([
                'users.index',
            ]);

            $operation = new Admin;
            $operation->email = 'operation@gotechjsc.com';
            $operation->password = bcrypt('gATO7CNZ6b');
            $operation->save();

            $operation->assignRole($operationRole);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
