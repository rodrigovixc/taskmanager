<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringTasks extends Command
{
    protected $signature = 'tasks:generate-recurring';
    protected $description = 'Gera tarefas recorrentes baseadas nas configurações de recorrência';

    public function handle()
    {
        $this->info('Iniciando geração de tarefas recorrentes...');

        $tasks = Task::where('recurrence', '!=', 'none')
            ->where('status', 'completed')
            ->where('due_date', '<', now())
            ->get();

        foreach ($tasks as $task) {
            $newDueDate = match ($task->recurrence) {
                'daily' => Carbon::parse($task->due_date)->addDay(),
                'weekly' => Carbon::parse($task->due_date)->addWeek(),
                'monthly' => Carbon::parse($task->due_date)->addMonth(),
                default => null
            };

            if ($newDueDate) {
                $newTask = $task->replicate();
                $newTask->status = 'pending';
                $newTask->due_date = $newDueDate;
                $newTask->save();

                // Copiar subtarefas
                foreach ($task->subtasks as $subtask) {
                    $newSubtask = $subtask->replicate();
                    $newSubtask->task_id = $newTask->id;
                    $newSubtask->is_completed = false;
                    $newSubtask->save();
                }

                $this->info("Tarefa recorrente gerada: {$newTask->title}");
            }
        }

        $this->info('Geração de tarefas recorrentes concluída!');
    }
} 