<?php

namespace Database\Seeders;

use App\Models\AffKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountSeeder2 extends Seeder
{
    /**
     * php artisan db:seed --class=AccountSeeder2
     */
    public function run(): void
    {
        for ($i = 70; $i <= 90; $i++) {
            $user = User::factory()->create([
                'name' => "User $i",
                'email' => "aiwow{$i}@gmail.com",
                'password' => Hash::make('123456'),
                'credit' => 300000,
                'vip_expired_at' => Carbon::create('2025', '12', '30', '23', '59', '00'),
            ]);

            AffKey::create([
                'config_aff_id' => 1,
                'key' => Str::random(12),
                'is_used' => false,
                'user_id' => $user->id,
            ]);
        }
    }
}
