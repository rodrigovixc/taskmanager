<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user', 'images'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $users = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'users' => $users
        ]);
    }
}
