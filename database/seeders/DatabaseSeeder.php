<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=DatabaseSeeder
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@gotechjsc.com',
            'password' => '123456'
        ]);

        // seed admins
        $admin = new Admin;
        $admin->email = 'admin@gotechjsc.com';
        $admin->password = bcrypt('12345678');
        $admin->save();
    }
}
