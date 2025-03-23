<?php

namespace App\Livewire;

use App\Models\Project;
use App\Traits\WithNotifications;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Rule;

class ProjectsList extends Component
{
    use WithNotifications;

    #[Rule(['required', 'string', 'max:255'])]
    public $form = [
        'name' => '',
        'description' => '',
        'color' => '#6366F1'
    ];

    public function createProject()
    {
        $validated = $this->validate([
            'form.name' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        Project::create([
            'name' => $validated['form']['name'],
            'description' => $validated['form']['description'],
            'color' => $validated['form']['color'],
            'created_by' => Auth::id()
        ]);

        $this->reset('form');
        $this->dispatch('close-create-modal');
        $this->notifySuccess('Projeto criado com sucesso!');
    }

    public function deleteProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $this->authorize('delete', $project);
        $project->delete();

        $this->dispatch('close-delete-modal');
        $this->notifySuccess('Projeto excluído com sucesso!');
    }

    public function render()
    {
        return view('livewire.projects-list', [
            'projects' => Project::where('created_by', Auth::id())
                ->withCount('tasks')
                ->with(['tasks' => function($query) {
                    $query->select('id', 'project_id', 'status');
                }])
                ->get()
                ->map(function($project) {
                    $project->completed_tasks = $project->tasks->where('status', 'completed')->count();
                    unset($project->tasks);
                    return $project;
                })
        ]);
    }
} 