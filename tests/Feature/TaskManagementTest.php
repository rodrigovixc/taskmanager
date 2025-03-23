use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('usuário autenticado pode acessar a página de tarefas', function () {
    $response = $this->actingAs($this->user)
        ->get(route('tasks.index'));

    $response->assertStatus(200);
});

test('usuário não autenticado é redirecionado para login', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect(route('login'));
});

test('usuário pode criar uma nova tarefa', function () {
    Storage::fake('public');

    $taskData = [
        'title' => 'Nova Tarefa de Teste',
        'description' => 'Descrição da tarefa de teste',
        'user_id' => $this->user->id,
        'image' => UploadedFile::fake()->image('task.jpg')
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Nova Tarefa de Teste',
        'description' => 'Descrição da tarefa de teste',
        'user_id' => $this->user->id,
        'status' => 'pendente'
    ]);

    $task = Task::first();
    $this->assertNotNull($task->images->first());
    Storage::disk('public')->assertExists($task->images->first()->path);
});

test('usuário não pode criar tarefa sem título', function () {
    $taskData = [
        'description' => 'Descrição da tarefa de teste',
        'user_id' => $this->user->id
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    $response->assertSessionHasErrors('title');
});

test('usuário pode importar múltiplas tarefas via texto', function () {
    $tasks = [
        [
            'title' => 'Tarefa Importada 1',
            'description' => 'Descrição da tarefa 1',
            'user_id' => $this->user->id
        ],
        [
            'title' => 'Tarefa Importada 2',
            'description' => 'Descrição da tarefa 2',
            'user_id' => $this->user->id
        ]
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.import'), ['tasks' => $tasks]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseCount('tasks', 2);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Tarefa Importada 1',
        'description' => 'Descrição da tarefa 1'
    ]);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Tarefa Importada 2',
        'description' => 'Descrição da tarefa 2'
    ]);
});

test('usuário pode atualizar o status de uma tarefa', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pendente'
    ]);

    $response = $this->actingAs($this->user)
        ->patch(route('tasks.update-status', $task), [
            'status' => 'em_andamento'
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'em_andamento'
    ]);
});

test('usuário não pode atualizar status para um valor inválido', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pendente'
    ]);

    $response = $this->actingAs($this->user)
        ->patch(route('tasks.update-status', $task), [
            'status' => 'status_invalido'
        ]);

    $response->assertSessionHasErrors('status');
});

test('usuário pode ver suas próprias tarefas', function () {
    $tasks = Task::factory()->count(3)->create([
        'user_id' => $this->user->id
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('tasks.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks', 3)
        ->where('tasks.0.user_id', $this->user->id)
    );
});

test('usuário pode criar tarefa sem imagem', function () {
    $taskData = [
        'title' => 'Tarefa Sem Imagem',
        'description' => 'Descrição da tarefa sem imagem',
        'user_id' => $this->user->id
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Tarefa Sem Imagem',
        'description' => 'Descrição da tarefa sem imagem'
    ]);

    $task = Task::first();
    $this->assertCount(0, $task->images);
});

test('usuário não pode enviar imagem maior que 10MB', function () {
    Storage::fake('public');

    $taskData = [
        'title' => 'Tarefa Com Imagem Grande',
        'description' => 'Descrição da tarefa',
        'user_id' => $this->user->id,
        'image' => UploadedFile::fake()->create('large_image.jpg', 11 * 1024) // 11MB
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), $taskData);

    $response->assertSessionHasErrors('image');
});

test('usuário pode importar tarefas mesmo sem descrição', function () {
    $tasks = [
        [
            'title' => 'Tarefa Sem Descrição',
            'user_id' => $this->user->id
        ]
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.import'), ['tasks' => $tasks]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Tarefa Sem Descrição',
        'description' => null
    ]);
});

test('usuário não pode importar tarefas sem título', function () {
    $tasks = [
        [
            'description' => 'Apenas descrição',
            'user_id' => $this->user->id
        ]
    ];

    $response = $this->actingAs($this->user)
        ->post(route('tasks.import'), ['tasks' => $tasks]);

    $response->assertSessionHasErrors('tasks.0.title');
});

test('tarefas são ordenadas por data de criação decrescente', function () {
    $oldTask = Task::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()->subDays(2)
    ]);

    $newTask = Task::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('tasks.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->where('tasks.0.id', $newTask->id)
        ->where('tasks.1.id', $oldTask->id)
    );
}); 