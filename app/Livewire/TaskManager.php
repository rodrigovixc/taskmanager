<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TaskManager extends Component
{
    public $tasks;
    public $projects;
    public $selectedProject = null;
    public $showTaskModal = false;
    public $editingTask = null;
    
    // Propriedades do formulário
    public $title = '';
    public $description = '';
    public $project_id = null;
    public $due_date = null;
    public $priority = 'medium';
    public $status = 'todo';

    protected $rules = [
        'title' => 'required|min:3',
        'description' => 'nullable',
        'project_id' => 'nullable|exists:projects,id',
        'due_date' => 'nullable|date',
        'priority' => 'required|in:low,medium,high',
        'status' => 'required|in:todo,in_progress,completed'
    ];

    public function mount()
    {
        $this->loadTasks();
        $this->projects = Project::where('user_id', auth()->id())->get();
    }

    public function loadTasks()
    {
        $query = Task::where('user_id', auth()->id());
        
        if ($this->selectedProject) {
            $query->where('project_id', $this->selectedProject);
        }
        
        $this->tasks = $query->with('project')->get()->groupBy('status');
    }

    public function filterByProject($projectId)
    {
        $this->selectedProject = $projectId;
        $this->loadTasks();
    }

    public function createTask()
    {
        $this->resetForm();
        $this->showTaskModal = true;
    }

    public function editTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        if ($task->user_id !== auth()->id()) {
            return;
        }

        $this->editingTask = $task;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->project_id = $task->project_id;
        $this->due_date = $task->due_date;
        $this->priority = $task->priority;
        $this->status = $task->status;
        $this->showTaskModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingTask) {
            if ($this->editingTask->user_id !== auth()->id()) {
                return;
            }
            
            $this->editingTask->update([
                'title' => $this->title,
                'description' => $this->description,
                'project_id' => $this->project_id,
                'due_date' => $this->due_date,
                'priority' => $this->priority,
                'status' => $this->status
            ]);
        } else {
            Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'project_id' => $this->project_id,
                'due_date' => $this->due_date,
                'priority' => $this->priority,
                'status' => $this->status,
                'user_id' => auth()->id()
            ]);
        }

        $this->resetForm();
        $this->loadTasks();
    }

    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        if ($task->user_id === auth()->id()) {
            $task->delete();
            $this->loadTasks();
        }
    }

    public function updateTaskStatus($taskId, $status)
    {
        $task = Task::findOrFail($taskId);
        if ($task->user_id === auth()->id()) {
            $task->update(['status' => $status]);
            $this->loadTasks();
        }
    }

    public function getTasksByStatus($status)
    {
        return $this->tasks[$status] ?? collect();
    }

    private function resetForm()
    {
        $this->editingTask = null;
        $this->title = '';
        $this->description = '';
        $this->project_id = null;
        $this->due_date = null;
        $this->priority = 'medium';
        $this->status = 'todo';
        $this->showTaskModal = false;
    }

    public function render()
    {
        return view('livewire.task-manager', [
            'todoTasks' => $this->getTasksByStatus('todo'),
            'inProgressTasks' => $this->getTasksByStatus('in_progress'),
            'completedTasks' => $this->getTasksByStatus('completed')
        ]);
    }
}
