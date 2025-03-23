<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TaskList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $project_id = '';
    public $orderBy = 'created_at';
    public $orderDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'project_id' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'orderDirection' => ['except' => 'desc']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->orderBy === $field) {
            $this->orderDirection = $this->orderDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderBy = $field;
            $this->orderDirection = 'asc';
        }
    }

    public function render()
    {
        $tasks = Task::query()
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->project_id, function ($query) {
                $query->where('project_id', $this->project_id);
            })
            ->orderBy($this->orderBy, $this->orderDirection)
            ->with(['project', 'subtasks'])
            ->paginate(10);

        $projects = Project::where('created_by', Auth::id())->get();

        return view('livewire.task-list', [
            'tasks' => $tasks,
            'projects' => $projects
        ])->extends('layouts.app');
    }
} 