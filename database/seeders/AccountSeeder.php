<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=AccountSeeder
     */
    public function run(): void
    {
        for ($i = 1; $i <= 60; $i++) {
            User::factory()->create([
                'name' => "User $i",
                'email' => "aiwow{$i}@gmail.com",
                'password' => Hash::make('123456'), // mã hóa password
                'credit' => 300000,
                'vip_expired_at' => Carbon::create('2025', '12', '30', '23', '59', '00'),
            ]);
        }
    }
}
