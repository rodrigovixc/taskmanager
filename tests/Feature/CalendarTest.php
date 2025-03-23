<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'in_progress',
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(30),
        ]);
    }

    public function test_calendar_page_requires_authentication(): void
    {
        $response = $this->get('/calendar');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_calendar(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/calendar');
            
        $response->assertOk();
        $response->assertSeeLivewire('calendar');
    }

    public function test_calendar_shows_tasks_with_due_dates(): void
    {
        $task = Task::create([
            'title' => 'Task with Due Date',
            'description' => 'Should appear in calendar',
            'status' => 'todo',
            'priority' => 'high',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/calendar');
            
        $response->assertSee('Task with Due Date');
        $response->assertSee($task->due_date->format('Y-m-d'));
    }

    public function test_calendar_shows_projects_with_due_dates(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/calendar');
            
        $response->assertSee('Test Project');
        $response->assertSee($this->project->due_date->format('Y-m-d'));
    }

    public function test_user_can_only_see_their_own_items_in_calendar(): void
    {
        $otherUser = User::factory()->create();
        
        $otherProject = Project::create([
            'name' => 'Other Project',
            'description' => 'Other Description',
            'status' => 'todo',
            'user_id' => $otherUser->id,
            'due_date' => now()->addDays(30),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/calendar');
            
        $response->assertSee('Test Project');
        $response->assertDontSee('Other Project');
    }

    public function test_calendar_shows_correct_date_ranges(): void
    {
        $today = now()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->get('/calendar');
            
        $response->assertSee($today);
        $response->assertSee($endOfMonth);
    }
} 