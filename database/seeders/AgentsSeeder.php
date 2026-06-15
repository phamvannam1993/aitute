<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agent::create(['name' => 'Đại lý A', 'email' => 'daily_a@example.com', 'phone' => '0123456789', 'address' => 'Hà Nội']);
        Agent::create(['name' => 'Đại lý B', 'email' => 'daily_b@example.com', 'phone' => '0987654321', 'address' => 'TP.HCM']);
        Agent::create(['name' => 'Đại lý C', 'email' => 'daily_c@example.com', 'phone' => '0912345678', 'address' => 'Đà Nẵng']);
    }
}
