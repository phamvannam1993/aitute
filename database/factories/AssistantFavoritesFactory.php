<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssistantFavorites>
 */
class AssistantFavoritesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return[
            'user_id' => 1,
            // random 1->200
            'ai_assistant_id' => $this->faker->numberBetween(1, 200),
        ];
    }
}
