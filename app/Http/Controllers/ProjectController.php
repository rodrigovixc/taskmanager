<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())
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
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|string|in:todo,in_progress,completed',
            'due_date' => 'nullable|date'
        ]);

        $validated['user_id'] = Auth::id();
        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($project);
    }

    public function edit(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Projects/Edit', [
            'project' => $project
        ]);
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|string|in:todo,in_progress,completed',
            'due_date' => 'nullable|date'
        ]);

        $project->update($validated);

        return response()->json($project, 200);
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
