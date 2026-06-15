<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionCollectionUpdateSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=PermissionCollectionUpdateSeeder
     */
    public function run(): void
    {
        try {
            // Tạo quyền cho keys
            //Role superadmin không cần gán quyền. Tự động apply tất cả quyền
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-collections.index'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-collections.create'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-collections.show'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-collections.edit'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-collections.destroy'],
                ['guard_name' => 'admin']
            );

            Permission::updateOrCreate(
                ['name' => 'my-ai-image-template-categories.index'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-template-categories.create'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-template-categories.show'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-template-categories.edit'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-template-categories.destroy'],
                ['guard_name' => 'admin']
            );

            Permission::updateOrCreate(
                ['name' => 'my-ai-image-templates.index'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-templates.create'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-templates.show'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-templates.edit'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'my-ai-image-templates.destroy'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'users.index'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'users.create'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'users.edit'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'users.destroy'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'users.add-credit'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'activity-logs.index'],
                ['guard_name' => 'admin']
            );

            Permission::updateOrCreate(
                ['name' => 'keys.index'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'keys.create'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'keys.edit'],
                ['guard_name' => 'admin']
            );
            Permission::updateOrCreate(
                ['name' => 'keys.destroy'],
                ['guard_name' => 'admin']
            );

            Permission::updateOrCreate(
                ['name' => 'users.export'],
                ['guard_name' => 'admin']
            );


            $operationRole = Role::where('name', 'operation')->first();

            $operationRole->givePermissionTo([
                'my-ai-image-collections.index',
                'my-ai-image-collections.create',
                'my-ai-image-collections.show',
                'my-ai-image-collections.edit',
                'my-ai-image-collections.destroy',
                'my-ai-image-template-categories.index',
                'my-ai-image-template-categories.create',
                'my-ai-image-template-categories.show',
                'my-ai-image-template-categories.edit',
                'my-ai-image-template-categories.destroy',
                'my-ai-image-templates.index',
                'my-ai-image-templates.create',
                'my-ai-image-templates.show',
                'my-ai-image-templates.edit',
                'my-ai-image-templates.destroy',

            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
