<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(),
            'color' => $this->faker->hexColor,
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'completed']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
} 