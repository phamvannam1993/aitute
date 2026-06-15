<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiAssistant;

class AiAssistantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // command php artisan db:seed --class=AiAssistantsSeeder
        // seed the ai_assistants table random 200 records
        AiAssistant::factory(200)->create();
    }
}
