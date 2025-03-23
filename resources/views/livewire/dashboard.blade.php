<div>
    <!-- Cabeçalho com botão de criar tarefa -->
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Minhas Tarefas</h2>
        <button wire:click="toggleTaskModal" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Tarefa
            </div>
        </button>
    </div>

    <!-- Grid do Kanban -->
    <div class="grid grid-cols-4 gap-4">
        @foreach($columns as $status => $title)
            <div class="bg-gray-100 rounded-lg p-4 kanban-column" data-status="{{ $status }}">
                <h3 class="font-semibold text-lg mb-4">{{ $title }}</h3>
                <div class="space-y-3">
                    @foreach($tasks[$status] ?? [] as $task)
                        <div wire:key="task-{{ $task->id }}" 
                             class="bg-white p-4 rounded-lg shadow cursor-pointer hover:shadow-md"
                             draggable="true"
                             data-task-id="{{ $task->id }}"
                             wire:dragstart="$dispatch('dragstart', { id: {{ $task->id }} })"
                             wire:dragend="$dispatch('dragend')"
                             wire:click="$dispatch('show-task', { taskId: {{ $task->id }} })">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $task->title }}</span>
                                @if($task->project)
                                    <span class="px-2 py-1 text-xs rounded-full" style="background-color: {{ $task->project->color }}20; color: {{ $task->project->color }}">
                                        {{ $task->project->name }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($task->description, 100) }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Sem data' }}</span>
                                <div class="flex items-center">
                                    @if($task->priority)
                                        <span class="mr-2 px-2 py-1 rounded-full {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    @endif
                                    @if($task->assignee)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $task->assignee->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
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

    <!-- Scripts para Drag and Drop -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const columns = document.querySelectorAll('.kanban-column');
            
            columns.forEach(column => {
                column.addEventListener('dragover', e => {
                    e.preventDefault();
                    const dragging = document.querySelector('.dragging');
                    if (dragging) {
                        column.appendChild(dragging);
                    }
                });
            });
        });

        window.addEventListener('dragstart', (e) => {
            if (e.target.matches('[draggable="true"]')) {
                e.target.classList.add('dragging', 'opacity-50');
            }
        });

        window.addEventListener('dragend', (e) => {
            if (e.target.matches('[draggable="true"]')) {
                e.target.classList.remove('dragging', 'opacity-50');
                const column = e.target.closest('.kanban-column');
                if (column) {
                    const status = column.dataset.status;
                    const taskId = e.target.dataset.taskId;
                    @this.updateTaskStatus(taskId, status);
                }
            }
        });
    </script>
</div> 