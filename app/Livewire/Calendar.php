<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use App\Traits\WithNotifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Calendar extends Component
{
    use WithNotifications;

    public $currentMonth;
    public $currentYear;
    public $selectedDate = null;
    public $tasks = [];
    public $projects = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->loadItems();
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadItems();
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadItems();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function closeModal()
    {
        $this->selectedDate = null;
    }

    public function toggleSubtask($taskId, $subtaskId)
    {
        $task = Task::findOrFail($taskId);
        $subtask = $task->subtasks()->findOrFail($subtaskId);
        $subtask->update(['is_completed' => !$subtask->is_completed]);
        $this->notifySuccess('Status da subtarefa atualizado!');
        $this->loadItems();
    }

    public function loadItems()
    {
        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Carrega as tarefas
        $this->tasks = Task::where('user_id', auth()->id())
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->with(['project', 'subtasks'])
            ->get()
            ->groupBy(function($task) {
                return $task->due_date ? $task->due_date->format('Y-m-d') : null;
            });

        // Carrega os projetos
        $this->projects = Project::where('user_id', auth()->id())
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function($project) {
                return $project->due_date ? $project->due_date->format('Y-m-d') : null;
            });
    }

    public function calendarDays()
    {
        $calendar = [];
        $today = Carbon::today();
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $date->daysInMonth;
        
        // Adiciona dias do mês anterior para preencher a primeira semana
        $firstDayOfWeek = $date->copy()->startOfMonth()->dayOfWeek;
        $previousMonth = $date->copy()->subMonth();
        $daysInPreviousMonth = $previousMonth->daysInMonth;
        
        for ($i = $firstDayOfWeek - 1; $i >= 0; $i--) {
            $currentDate = $previousMonth->copy()->setDay($daysInPreviousMonth - $i);
            $calendar[] = [
                'date' => $currentDate->format('Y-m-d'),
                'isCurrentMonth' => false,
                'isToday' => $currentDate->isSameDay($today)
            ];
        }
        
        // Adiciona dias do mês atual
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = $date->copy()->setDay($day);
            $calendar[] = [
                'date' => $currentDate->format('Y-m-d'),
                'isCurrentMonth' => true,
                'isToday' => $currentDate->isSameDay($today)
            ];
        }
        
        // Adiciona dias do próximo mês para completar a última semana
        $lastDayOfWeek = $date->copy()->endOfMonth()->dayOfWeek;
        $nextMonth = $date->copy()->addMonth()->startOfMonth();
        
        for ($i = 1; $i <= (6 - $lastDayOfWeek); $i++) {
            $currentDate = $nextMonth->copy()->setDay($i);
            $calendar[] = [
                'date' => $currentDate->format('Y-m-d'),
                'isCurrentMonth' => false,
                'isToday' => $currentDate->isSameDay($today)
            ];
        }
        
        return $calendar;
    }

    public function render()
    {
        return view('livewire.calendar', [
            'calendar' => $this->calendarDays()
        ]);
    }
} 