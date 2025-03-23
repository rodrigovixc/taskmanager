<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $viewMode = 'list';
    public $tasks;
    public $projects;
    public $tasksByStatus;
    public $showTaskModal = false;
    public $columns = [
        'backlog' => 'Backlog',
        'todo' => 'A Fazer',
        'in_progress' => 'Em Progresso',
        'done' => 'Concluído'
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->tasks = Task::where('user_id', Auth::id())
            ->with(['project', 'subtasks', 'comments', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->projects = Project::where('user_id', Auth::id())
            ->withCount('tasks')
            ->get();

        $this->tasksByStatus = [
            'backlog' => $this->tasks->where('status', 'backlog')->values(),
            'todo' => $this->tasks->where('status', 'todo')->values(),
            'in_progress' => $this->tasks->where('status', 'in_progress')->values(),
            'done' => $this->tasks->where('status', 'done')->values()
        ];
    }

    public function toggleView($mode)
    {
        $this->viewMode = $mode;
    }

    public function loadTasks()
    {
        $this->tasks = Task::where('user_id', auth()->id())
            ->with('project')
            ->get()
            ->groupBy('status');
    }

    public function updateTaskStatus($taskId, $status)
    {
        $task = Task::find($taskId);
        if ($task && $task->user_id === auth()->id()) {
            $task->update(['status' => $status]);
            $this->loadTasks();
        }
    }

    public function toggleTaskModal()
    {
        $this->showTaskModal = !$this->showTaskModal;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'totalTasks' => $this->tasks->count(),
            'pendingTasks' => $this->tasks->where('status', 'todo')->count(),
            'completedTasks' => $this->tasks->where('status', 'done')->count(),
            'overdueTasks' => $this->tasks->where('due_date', '<', now())->where('status', '!=', 'done')->count()
        ]);
    }
} 