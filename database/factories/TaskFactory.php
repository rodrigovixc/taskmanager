<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
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
