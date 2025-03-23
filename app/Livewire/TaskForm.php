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

    public function mount()
    {
        $this->projects = Project::where('user_id', auth()->id())->get();
    }

    public function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'nullable',
            'project_id' => 'nullable|exists:projects,id',
            'due_date' => 'nullable|date|after:today',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:backlog,todo,in_progress,done'
        ];
    }

    public function save()
    {
        $this->validate();

        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->project_id,
            'due_date' => $this->due_date,
            'priority' => $this->priority,
            'status' => $this->status,
            'user_id' => auth()->id()
        ]);

        $this->reset(['title', 'description', 'project_id', 'due_date']);
        $this->dispatch('task-created');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
