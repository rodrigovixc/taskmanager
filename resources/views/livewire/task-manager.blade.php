<div class="p-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Gerenciador de Tarefas</h2>
            <button wire:click="createTask" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Nova Tarefa
            </button>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Filtrar por Projeto</label>
            <select wire:model.live="selectedProject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos os Projetos</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- To Do -->
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">A Fazer</h3>
            <div class="space-y-4">
                @foreach($todoTasks as $task)
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium">{{ $task->title }}</h4>
                            <div class="flex space-x-2">
                                <button wire:click="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="deleteTask({{ $task->id }})" class="text-red-500 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $task->description }}</p>
                        @if($task->project)
                            <div class="mt-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $task->project->name }}</span>
                            </div>
                        @endif
                        <div class="mt-2 flex justify-between items-center">
                            <span class="text-xs text-gray-500">{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Sem data' }}</span>
                            <span class="text-xs px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Em Progresso -->
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Em Progresso</h3>
            <div class="space-y-4">
                @foreach($inProgressTasks as $task)
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium">{{ $task->title }}</h4>
                            <div class="flex space-x-2">
                                <button wire:click="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="deleteTask({{ $task->id }})" class="text-red-500 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $task->description }}</p>
                        @if($task->project)
                            <div class="mt-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $task->project->name }}</span>
                            </div>
                        @endif
                        <div class="mt-2 flex justify-between items-center">
                            <span class="text-xs text-gray-500">{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Sem data' }}</span>
                            <span class="text-xs px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Concluídas -->
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Concluídas</h3>
            <div class="space-y-4">
                @foreach($completedTasks as $task)
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium">{{ $task->title }}</h4>
                            <div class="flex space-x-2">
                                <button wire:click="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="deleteTask({{ $task->id }})" class="text-red-500 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $task->description }}</p>
                        @if($task->project)
                            <div class="mt-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $task->project->name }}</span>
                            </div>
                        @endif
                        <div class="mt-2 flex justify-between items-center">
                            <span class="text-xs text-gray-500">{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Sem data' }}</span>
                            <span class="text-xs px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal de Tarefa -->
    @if($showTaskModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-lg w-full">
                <h3 class="text-lg font-semibold mb-4">{{ $editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}</h3>
                
                <form wire:submit="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Projeto</label>
                            <select wire:model="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecione um projeto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Entrega</label>
                            <input type="date" wire:model="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prioridade</label>
                            <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                            @error('priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="todo">A Fazer</option>
                                <option value="in_progress">Em Progresso</option>
                                <option value="completed">Concluída</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="resetForm" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            {{ $editingTask ? 'Atualizar' : 'Criar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
