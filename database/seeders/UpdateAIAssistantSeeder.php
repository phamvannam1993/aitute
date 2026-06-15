<?php

namespace Database\Seeders;

use App\Models\AiAssistant;
use Illuminate\Database\Seeder;

class UpdateAIAssistantSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=UpdateAIAssistantSeeder
     */
    public function run(): void
    {
        $assistants = AiAssistant::all();

        foreach ($assistants as $assistant) {
            switch ($assistant->type) {
                case 1:
                    $assistant->type = 'text';
                    break;
                case 2:
                    $assistant->type = 'image';
                    break;
                case 3:
                    $assistant->type = 'video';
                    break;
            }

            $assistant->save();
        }
    }
}
