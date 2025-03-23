<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Collection;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $viewMode = 'list';
    public Collection $tasks;
    public Collection $projects;
    public array $tasksByStatus = [];
    public bool $showTaskModal = false;
    public array $columns = [
        'todo' => 'A Fazer',
        'in_progress' => 'Em Progresso',
        'completed' => 'Concluído'
    ];

    protected $listeners = [
        'task-created' => 'loadData',
        'task-updated' => 'loadData',
        'close-modal' => 'closeModal'
    ];

    public function mount()
    {
        $this->tasks = collect();
        $this->projects = collect();
        $this->loadData();
    }

    public function loadData()
    {
        try {
            // Carregar tarefas com relacionamentos
            $this->tasks = Task::where('user_id', Auth::id())
                ->with(['project', 'subtasks', 'comments', 'attachments'])
                ->orderBy('due_date', 'asc')
                ->get();

            // Carregar projetos com contagem de tarefas concluídas
            $this->projects = Project::where('user_id', Auth::id())
                ->withCount(['tasks as total_tasks'])
                ->withCount(['tasks as completed_tasks' => function($query) {
                    $query->where('status', 'completed');
                }])
                ->get()
                ->map(function($project) {
                    $project->progress = $project->total_tasks > 0 ? 
                        round(($project->completed_tasks / $project->total_tasks) * 100) : 0;
                    return $project;
                });

            // Agrupar tarefas por status
            $this->tasksByStatus = [];
            foreach ($this->columns as $status => $title) {
                $this->tasksByStatus[$status] = $this->tasks
                    ->filter(fn($task) => $task->status === $status)
                    ->values()
                    ->all();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro ao carregar os dados. Por favor, tente novamente.');
        }
    }

    public function toggleView($mode)
    {
        if (in_array($mode, ['list', 'board'])) {
            $this->viewMode = $mode;
        }
    }

    public function updateTaskStatus($taskId, $status)
    {
        try {
            $task = Task::find($taskId);
            if ($task && $task->user_id === auth()->id() && array_key_exists($status, $this->columns)) {
                $task->update([
                    'status' => $status,
                    'updated_at' => now()
                ]);
                $this->loadData();
                $this->dispatch('task-updated');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro ao atualizar a tarefa. Por favor, tente novamente.');
        }
    }

    public function toggleTaskModal()
    {
        $this->showTaskModal = !$this->showTaskModal;
    }

    public function closeModal()
    {
        $this->showTaskModal = false;
        $this->loadData();
    }

    public function getStatistics()
    {
        if ($this->tasks->isEmpty()) {
            return [
                'totalTasks' => 0,
                'pendingTasks' => 0,
                'completedTasks' => 0,
                'overdueTasks' => 0,
                'upcomingTasks' => 0
            ];
        }

        return [
            'totalTasks' => $this->tasks->count(),
            'pendingTasks' => $this->tasks->whereIn('status', ['todo', 'in_progress'])->count(),
            'completedTasks' => $this->tasks->where('status', 'completed')->count(),
            'overdueTasks' => $this->tasks->filter(function ($task) {
                return $task->due_date 
                    && $task->due_date->isPast() 
                    && in_array($task->status, ['todo', 'in_progress']);
            })->count(),
            'upcomingTasks' => $this->tasks->filter(function ($task) {
                return $task->due_date 
                    && $task->due_date->isFuture() 
                    && $task->due_date->lte(now()->addDays(7))
                    && in_array($task->status, ['todo', 'in_progress']);
            })->count()
        ];
    }

    public function render()
    {
        return view('livewire.dashboard', array_merge(
            [
                'viewMode' => $this->viewMode,
                'error' => session('error')
            ],
            $this->getStatistics()
        ));
    }
} 