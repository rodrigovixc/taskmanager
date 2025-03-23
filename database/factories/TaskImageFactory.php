<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskImage>
 */
class TaskImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = $this->faker->uuid() . '.jpg';
        
        // Cria um arquivo de imagem falso no storage
        Storage::fake('public');
        $path = 'task-images/' . $fileName;
        Storage::disk('public')->put($path, file_get_contents($this->faker->image()));

        return [
            'task_id' => Task::factory(),
            'path' => $path,
            'original_name' => $this->faker->word() . '.jpg'
        ];
    }
}
