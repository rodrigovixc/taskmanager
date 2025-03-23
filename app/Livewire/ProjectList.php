<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProjectList extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $name = '';
    public $description = '';
    public $color = '#4F46E5'; // Cor padrão indigo-600
    public $status = 'todo';
    public $editing = null;
    public $due_date = null;

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'nullable',
        'color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
        'status' => 'required|in:todo,in_progress,completed',
        'due_date' => 'nullable|date'
    ];

    public function save()
    {
        $this->validate();

        if ($this->editing) {
            $project = Project::findOrFail($this->editing);
            if ($project && $project->user_id === auth()->id()) {
                $project->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'color' => $this->color,
                    'status' => $this->status,
                    'due_date' => $this->due_date
                ]);
                session()->flash('message', 'Projeto atualizado com sucesso!');
            }
        } else {
            Project::create([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'status' => $this->status,
                'due_date' => $this->due_date,
                'user_id' => auth()->id()
            ]);
            session()->flash('message', 'Projeto criado com sucesso!');
        }

        $this->reset(['name', 'description', 'color', 'status', 'due_date', 'showModal', 'editing']);
    }

    public function edit($id)
    {
        $project = Project::find($id);
        if ($project && $project->user_id === auth()->id()) {
            $this->editing = $project->id;
            $this->name = $project->name;
            $this->description = $project->description;
            $this->color = $project->color;
            $this->status = $project->status;
            $this->due_date = $project->due_date;
            $this->showModal = true;
        }
    }

    public function delete($id)
    {
        $project = Project::find($id);
        if ($project && $project->user_id === auth()->id()) {
            $project->delete();
            session()->flash('message', 'Projeto excluído com sucesso!');
        }
    }

    public function render()
    {
        $projects = Project::where('user_id', auth()->id())
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.project-list', [
            'projects' => $projects
        ]);
    }
} 