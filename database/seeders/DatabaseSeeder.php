<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Criar alguns projetos
        $projects = [
            [
                'name' => 'Projeto Website',
                'description' => 'Desenvolvimento do novo website da empresa',
                'status' => 'in_progress',
                'due_date' => now()->addDays(30),
            ],
            [
                'name' => 'App Mobile',
                'description' => 'Desenvolvimento do aplicativo mobile',
                'status' => 'todo',
                'due_date' => now()->addDays(60),
            ],
            [
                'name' => 'Sistema de RH',
                'description' => 'Sistema interno de recursos humanos',
                'status' => 'backlog',
                'due_date' => now()->addDays(90),
            ]
        ];

        foreach ($projects as $project) {
            $newProject = $admin->projects()->create($project);

            // Criar tarefas para cada projeto
            $tasks = [
                [
                    'title' => 'Análise de Requisitos',
                    'description' => 'Levantar e documentar todos os requisitos',
                    'status' => 'todo',
                    'priority' => 'high',
                    'due_date' => now()->addDays(7),
                ],
                [
                    'title' => 'Design de Interface',
                    'description' => 'Criar o design das telas principais',
                    'status' => 'in_progress',
                    'priority' => 'medium',
                    'due_date' => now()->addDays(14),
                ],
                [
                    'title' => 'Desenvolvimento Backend',
                    'description' => 'Implementar a API e banco de dados',
                    'status' => 'backlog',
                    'priority' => 'high',
                    'due_date' => now()->addDays(21),
                ],
                [
                    'title' => 'Testes',
                    'description' => 'Realizar testes de integração e unitários',
                    'status' => 'todo',
                    'priority' => 'medium',
                    'due_date' => now()->addDays(28),
                ]
            ];

            foreach ($tasks as $task) {
                $newProject->tasks()->create(array_merge($task, ['user_id' => $admin->id]));
            }
        }
    }
}
