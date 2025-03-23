<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Policies\Controllers\Controller;use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $tasks = Task::where('user_id', Auth::id())
            ->whereMonth('due_date', $month)
            ->whereYear('due_date', $year)
            ->with(['project', 'subtasks'])
            ->get()
            ->groupBy(function($task) {
                return Carbon::parse($task->due_date)->format('Y-m-d');
            });

        $calendar = $this->generateCalendar($month, $year);

        return Inertia::render('Calendar', [
            'tasks' => $tasks,
            'calendar' => $calendar,
            'currentMonth' => $month,
            'currentYear' => $year
        ]);
    }

    private function generateCalendar($month, $year)
    {
        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $firstDayOfWeek = $date->copy()->firstOfMonth()->dayOfWeek;

        $calendar = [];
        $day = 1;

        // Preenche os dias do mês anterior
        if ($firstDayOfWeek > 0) {
            $lastMonth = $date->copy()->subMonth();
            $lastMonthDays = $lastMonth->daysInMonth;
            $start = $lastMonthDays - $firstDayOfWeek + 1;

            for ($i = $start; $i <= $lastMonthDays; $i++) {
                $calendar[] = [
                    'day' => $i,
                    'month' => $lastMonth->month,
                    'year' => $lastMonth->year,
                    'isCurrentMonth' => false
                ];
            }
        }

        // Preenche os dias do mês atual
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $calendar[] = [
                'day' => $i,
                'month' => $month,
                'year' => $year,
                'isCurrentMonth' => true
            ];
        }

        // Preenche os dias do próximo mês
        $remainingDays = 42 - count($calendar); // 6 semanas x 7 dias = 42
        if ($remainingDays > 0) {
            $nextMonth = $date->copy()->addMonth();
            for ($i = 1; $i <= $remainingDays; $i++) {
                $calendar[] = [
                    'day' => $i,
                    'month' => $nextMonth->month,
                    'year' => $nextMonth->year,
                    'isCurrentMonth' => false
                ];
            }
        }

        return $calendar;
    }
}
