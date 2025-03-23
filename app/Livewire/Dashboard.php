<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $viewMode = 'list';
    public $tasks;
    public $projects;
    public $tasksByStatus = [];
    public $showTaskModal = false;
    public $columns = [
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
        $this->loadData();
    }

    public function loadData()
    {
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
        foreach ($this->columns as $status => $title) {
            $this->tasksByStatus[$status] = $this->tasks->filter(function ($task) use ($status) {
                return $task->status === $status;
            })->values()->all();
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
        $task = Task::find($taskId);
        if ($task && $task->user_id === auth()->id() && array_key_exists($status, $this->columns)) {
            $task->update([
                'status' => $status,
                'updated_at' => now()
            ]);
            $this->loadData();
            $this->dispatch('task-updated');
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
        return [
            'totalTasks' => $this->tasks->count(),
            'pendingTasks' => $this->tasks->whereIn('status', ['todo', 'in_progress'])->count(),
            'completedTasks' => $this->tasks->where('status', 'completed')->count(),
            'overdueTasks' => $this->tasks->where('due_date', '<', now())
                ->whereIn('status', ['todo', 'in_progress'])
                ->count(),
            'upcomingTasks' => $this->tasks->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(7))
                ->whereIn('status', ['todo', 'in_progress'])
                ->count()
        ];
    }

    public function render()
    {
        return view('livewire.dashboard', array_merge(
            ['viewMode' => $this->viewMode],
            $this->getStatistics()
        ));
    }
} 