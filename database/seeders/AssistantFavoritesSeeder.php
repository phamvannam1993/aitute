<?php

namespace Database\Seeders;

use App\Models\AssistantFavorites;
use Illuminate\Database\Seeder;

class AssistantFavoritesSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=AssistantFavoritesSeeder
     */
    public function run(): void
    {
        AssistantFavorites::factory(200)->create();
    }
}
