<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pendente', 'em_andamento', 'concluida'])
        ];
    }

    public function pendente()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pendente'
            ];
        });
    }

    public function emAndamento()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'em_andamento'
            ];
        });
    }

    public function concluida()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'concluida'
            ];
        });
    }
}
