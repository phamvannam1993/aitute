<?php

namespace Database\Seeders;

use App\Models\AffKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountSeeder3 extends Seeder
{
    /**
     * php artisan db:seed --class=AccountSeeder3
     */
    public function run(): void
    {
        for ($i = 1; $i <= 60; $i++) {
            $user = User::factory()->create([
                'name' => "User $i",
                'email' => "user{$i}@lurcinn.com",
                'password' => Hash::make('123456'),
                'credit' => 300000,
                'vip_expired_at' => Carbon::create('2025', '12', '30', '23', '59', '00'),
            ]);

            AffKey::create([
                'config_aff_id' => 1,
                'key' => Str::random(12),
                'is_used' => true,
                'user_id' => $user->id,
            ]);
        }
    }
}
