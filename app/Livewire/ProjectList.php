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

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'nullable',
        'color' => 'required|regex:/^#[a-fA-F0-9]{6}$/'
    ];

    public function createProject()
    {
        $this->validate();

        Project::create([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'user_id' => auth()->id()
        ]);

        $this->reset(['name', 'description', 'color', 'showModal']);
        session()->flash('message', 'Projeto criado com sucesso!');
    }

    public function deleteProject($id)
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