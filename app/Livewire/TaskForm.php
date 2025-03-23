<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;

class TaskForm extends Component
{
    public $title;
    public $description;
    public $project_id;
    public $due_date;
    public $priority = 'medium';
    public $status = 'todo';
    public $projects;
    public $editing = false;
    public $taskId;

    public function mount($taskId = null)
    {
        $this->projects = Project::where('user_id', auth()->id())->get();
        
        if ($taskId) {
            $this->taskId = $taskId;
            $this->editing = true;
            $this->loadTask();
        }
    }

    public function loadTask()
    {
        $task = Task::findOrFail($this->taskId);
        $this->title = $task->title;
        $this->description = $task->description;
        $this->project_id = $task->project_id;
        $this->due_date = $task->due_date;
        $this->priority = $task->priority;
        $this->status = $task->status;
    }

    public function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'nullable',
            'project_id' => 'nullable|exists:projects,id',
            'due_date' => 'nullable|date|after:today',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,completed'
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->editing) {
            $task = Task::findOrFail($this->taskId);
            $task->update([
                'title' => $this->title,
                'description' => $this->description,
                'project_id' => $this->project_id,
                'due_date' => $this->due_date,
                'priority' => $this->priority,
                'status' => $this->status
            ]);
            $this->dispatch('task-updated');
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
            $this->dispatch('task-created');
        }

        $this->reset(['title', 'description', 'project_id', 'due_date', 'editing', 'taskId']);
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
