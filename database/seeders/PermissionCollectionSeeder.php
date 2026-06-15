<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionCollectionSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=PermissionCollectionSeeder
     */
    public function run(): void
    {
        try {
            // Tạo quyền cho keys
            //Role superadmin không cần gán quyền. Tự động apply tất cả quyền
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-collections.index']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-collections.create']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-collections.show']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-collections.edit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-collections.destroy']);

            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-template-categories.index']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-template-categories.create']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-template-categories.show']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-template-categories.edit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-template-categories.destroy']);

            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-templates.index']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-templates.create']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-templates.show']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-templates.edit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'my-ai-image-templates.destroy']);


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
