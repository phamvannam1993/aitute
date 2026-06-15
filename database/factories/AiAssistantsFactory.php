<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiAssistants>
 */
class AiAssistantsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => 1,
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(['chatbot', 'voicebot']),
            'description' => $this->faker->text(),
            'thumbnail' => $this->faker->imageUrl(),
            'prompt' => $this->faker->text(),
            'occupation_id' => rand(1, 40),
            'operation_id' => rand(1, 200),
        ];
    }
}
