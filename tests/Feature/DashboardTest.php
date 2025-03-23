<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'in_progress',
            'user_id' => $this->user->id,
            'color' => '#4A5568',
            'due_date' => now()->addDays(30),
        ]);
        
        $this->task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'high',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_dashboard_page_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard');
            
        $response->assertOk();
        $response->assertSeeLivewire('dashboard');
    }

    public function test_dashboard_shows_statistics(): void
    {
        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('Total de Tarefas')
            ->assertSee('Tarefas Pendentes')
            ->assertSee('Tarefas Concluídas')
            ->assertSee('Tarefas Atrasadas')
            ->assertSee('1'); // Total de tarefas
    }

    public function test_dashboard_shows_user_projects(): void
    {
        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('Test Project')
            ->assertSee('Test Description')
            ->assertSee('Em Progresso')
            ->assertSee('Progresso')
            ->assertSee('0%'); // 0% pois não há tarefas concluídas
    }

    public function test_dashboard_shows_user_tasks(): void
    {
        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('Test Task')
            ->assertSee('Test Description')
            ->assertSee('High');
    }

    public function test_dashboard_shows_empty_state_for_no_projects(): void
    {
        $this->project->delete();

        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('Nenhum projeto encontrado');
    }

    public function test_dashboard_shows_task_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->call('toggleTaskModal')
            ->assertSee('Nova Tarefa');
    }

    public function test_dashboard_shows_project_progress(): void
    {
        Task::create([
            'title' => 'Completed Task',
            'description' => 'Test Description',
            'status' => 'completed',
            'priority' => 'high',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('50%'); // 1 de 2 tarefas concluídas = 50%
    }

    public function test_dashboard_updates_task_status(): void
    {
        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->call('updateTaskStatus', $this->task->id, 'in_progress')
            ->assertDispatched('task-updated');

        $this->assertEquals('in_progress', $this->task->fresh()->status);
    }

    public function test_dashboard_shows_upcoming_tasks(): void
    {
        // Criar uma tarefa para próxima semana
        Task::create([
            'title' => 'Upcoming Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(5),
        ]);

        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('2'); // 2 tarefas pendentes para os próximos 7 dias
    }

    public function test_dashboard_shows_overdue_tasks(): void
    {
        // Criar uma tarefa atrasada
        Task::create([
            'title' => 'Overdue Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'high',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->subDays(2),
        ]);

        Livewire::actingAs($this->user)
            ->test('dashboard')
            ->assertSee('1'); // 1 tarefa atrasada
    }

    public function test_dashboard_can_toggle_view_mode(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test('dashboard');

        $component->call('toggleView', 'board')
            ->assertSet('viewMode', 'board');

        $component->call('toggleView', 'list')
            ->assertSet('viewMode', 'list');

        // Modo inválido não deve alterar o viewMode
        $component->call('toggleView', 'invalid')
            ->assertSet('viewMode', 'list');
    }
} 