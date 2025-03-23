<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'nullable|string|in:todo,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date',
            'image' => 'nullable|file|image|max:10240' // 10MB max
        ]);

        $task = new Task($validated);
        $task->user_id = Auth::id();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('task-images', 'public');
            $task->image = $path;
        }
        
        $task->save();

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'nullable|string|in:todo,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date',
            'image' => 'nullable|file|image|max:10240' // 10MB max
        ]);

        if ($request->hasFile('image')) {
            if ($task->image) {
                Storage::disk('public')->delete($task->image);
            }
            $path = $request->file('image')->store('task-images', 'public');
            $validated['image'] = $path;
        }

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->image) {
            Storage::disk('public')->delete($task->image);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function updateStatus(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:todo,in_progress,completed'
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function import(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string'
        ]);

        $tasks = collect($request->tasks)->map(function ($taskData) {
            return Auth::user()->tasks()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null
            ]);
        });

        return response()->json($tasks, 201);
    }
} 