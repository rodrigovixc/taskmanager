<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Rule;

class TaskForm extends Component
{
    #[Rule('required|min:3')]
    public $title = '';

    #[Rule('nullable|string')]
    public $description = '';

    #[Rule('nullable|exists:projects,id')]
    public $project_id;

    #[Rule('nullable|date')]
    public $due_date;

    #[Rule('required|in:low,medium,high')]
    public $priority = 'medium';

    #[Rule('required|in:todo,in_progress,completed')]
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
        
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $this->title = $task->title;
        $this->description = $task->description;
        $this->project_id = $task->project_id;
        $this->due_date = $task->due_date;
        $this->priority = $task->priority;
        $this->status = $task->status;
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            if ($this->editing) {
                $task = Task::findOrFail($this->taskId);
                
                if ($task->user_id !== auth()->id()) {
                    abort(403);
                }

                $task->update($validated);
                $this->dispatch('task-updated');
            } else {
                Task::create(array_merge($validated, [
                    'user_id' => auth()->id()
                ]));
                $this->dispatch('task-created');
            }

            $this->reset(['title', 'description', 'project_id', 'due_date']);
            $this->priority = 'medium';
            $this->status = 'todo';
            $this->dispatch('close-modal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro ao salvar a tarefa. Por favor, tente novamente.');
        }
    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
