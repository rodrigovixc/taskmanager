<div>
    @if($error)
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $error }}
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total de Tarefas</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalTasks }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Tarefas Pendentes</div>
                        <div class="mt-1 text-3xl font-semibold text-yellow-600">{{ $pendingTasks }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Tarefas Concluídas</div>
                        <div class="mt-1 text-3xl font-semibold text-green-600">{{ $completedTasks }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Tarefas Atrasadas</div>
                        <div class="mt-1 text-3xl font-semibold text-red-600">{{ $overdueTasks }}</div>
                    </div>
                </div>
            </div>

            <!-- Projetos -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Meus Projetos</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($projects as $project)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ $project->description }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $project->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($project->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                    }}">
                                        {{ $project->status === 'completed' ? 'Concluído' : ($project->status === 'in_progress' ? 'Em Progresso' : 'A Fazer') }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <div class="flex justify-between text-sm text-gray-500 mb-1">
                                        <span>Progresso</span>
                                        <span>{{ $project->progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                </div>
                                @if($project->due_date)
                                    <div class="mt-4 text-sm text-gray-500">
                                        Data de Entrega: {{ \Carbon\Carbon::parse($project->due_date)->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3">
                            <div class="text-center py-4 text-gray-500">
                                Nenhum projeto encontrado.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tarefas -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Minhas Tarefas</h2>
                    <button wire:click="toggleTaskModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Nova Tarefa
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($columns as $status => $title)
                        <div class="bg-gray-100 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 mb-4">{{ $title }}</h3>
                            <div class="space-y-4">
                                @forelse($tasksByStatus[$status] ?? [] as $task)
                                    <div class="bg-white p-4 rounded-lg shadow" wire:key="task-{{ $task->id }}">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                                                <p class="mt-1 text-sm text-gray-500">{{ $task->description }}</p>
                                            </div>
                                            @if($task->project)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $task->project->color }}20; color: {{ $task->project->color }}">
                                                    {{ $task->project->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-4 flex justify-between items-center text-sm">
                                            <span class="text-gray-500">
                                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : 'Sem data' }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                                ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')
                                            }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-gray-500">
                                        Nenhuma tarefa {{ strtolower($title) }}.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Nova Tarefa -->
    @if($showTaskModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Nova Tarefa</h3>
                    <button wire:click="toggleTaskModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <livewire:task-form />
            </div>
        </div>
    @endif
</div> 