<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Policies\Controllers\Controller;use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('created_by', Auth::id())
            ->withCount('tasks')
            ->with(['tasks' => function($query) {
                $query->select('id', 'project_id', 'status');
            }])
            ->get()
            ->map(function($project) {
                $project->completed_tasks = $project->tasks->where('status', 'completed')->count();
                unset($project->tasks);
                return $project;
            });

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    public function create()
    {
        return Inertia::render('Projects/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'created_by' => Auth::id()
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['tasks' => function($query) {
            $query->with(['user', 'subtasks', 'comments.user'])
                ->orderBy('created_at', 'desc');
        }]);

        return Inertia::render('Projects/Show', [
            'project' => $project
        ]);
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => $project
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projeto excluído com sucesso!');
    }
}
