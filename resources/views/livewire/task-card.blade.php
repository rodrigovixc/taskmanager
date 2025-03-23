<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 relative {{ $task->status === 'completed' ? 'border-l-4 border-green-500' : ($task->status === 'in_progress' ? 'border-l-4 border-blue-500' : 'border-l-4 border-yellow-500') }}">
    
    <!-- Cabeçalho do Card -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
            @if($editMode)
                <input 
                    type="text" 
                    wire:model="editedTask.title" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            @else
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <button wire:click="toggleDetails" class="hover:text-indigo-600">
                        {{ $task->title }}
                    </button>
                    @if($task->project)
                        <span class="ml-2 px-2 py-1 text-xs rounded-full" style="background-color: {{ $task->project->color }}">
                            {{ $task->project->name }}
                        </span>
                    @endif
                </h3>
            @endif
        </div>

        <!-- Ações Rápidas -->
        <div class="flex items-center space-x-2">
            <!-- Status Toggle -->
            <div class="relative">
                <button 
                    class="p-1 rounded-full {{ $task->status === 'completed' ? 'text-green-600 hover:text-green-700' : 'text-gray-400 hover:text-gray-500' }}"
                    wire:click="updateStatus('{{ $task->status === 'completed' ? 'pending' : 'completed' }}')"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>

            <!-- Editar -->
            <button 
                wire:click="toggleEditMode"
                class="p-1 text-gray-400 hover:text-gray-500"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Detalhes da Tarefa -->
    <div class="mb-3 flex items-center justify-between text-sm text-gray-500">
        <div class="flex items-center space-x-4">
            <!-- Data de Vencimento -->
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                @if($editMode)
                    <input 
                        type="date" 
                        wire:model="editedTask.due_date"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                @else
                    <span>{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Sem data' }}</span>
                @endif
            </div>

            <!-- Subtarefas -->
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>{{ $completedSubtasksCount }}/{{ $totalSubtasksCount }}</span>
            </div>

            <!-- Anexos -->
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                <span>{{ $attachments->count() }}</span>
            </div>
        </div>

        <!-- Responsável -->
        <div class="flex items-center">
            <img 
                src="https://ui-avatars.com/api/?name={{ urlencode($task->user->name) }}&color=7F9CF5&background=EBF4FF" 
                alt="{{ $task->user->name }}"
                class="h-6 w-6 rounded-full"
            >
            <span class="ml-2">{{ $task->user->name }}</span>
        </div>
    </div>

    <!-- Detalhes Expandidos -->
    @if($showDetails)
        <div class="mt-4 space-y-4">
            <!-- Descrição -->
            @if($editMode)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea 
                        wire:model="editedTask.description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    ></textarea>
                </div>
            @else
                @if($task->description)
                    <div class="prose max-w-none text-gray-700">
                        {{ $task->description }}
                    </div>
                @endif
            @endif

            <!-- Subtarefas -->
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Subtarefas</h4>
                <div class="space-y-2">
                    @foreach($subtasks as $subtask)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:click="toggleSubtask({{ $subtask->id }})"
                                    {{ $subtask->is_completed ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <span class="ml-2 {{ $subtask->is_completed ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                    {{ $subtask->title }}
                                </span>
                            </div>
                            <button 
                                wire:click="deleteSubtask({{ $subtask->id }})"
                                class="text-red-500 hover:text-red-700"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Nova Subtarefa -->
                <div class="mt-2">
                    <div class="flex">
                        <input 
                            type="text"
                            wire:model="newSubtaskTitle"
                            wire:keydown.enter="addSubtask"
                            placeholder="Adicionar subtarefa..."
                            class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <button
                            wire:click="addSubtask"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Comentários -->
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Comentários</h4>
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="flex space-x-3">
                            <div class="flex-shrink-0">
                                <img 
                                    src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&color=7F9CF5&background=EBF4FF" 
                                    alt="{{ $comment->user->name }}"
                                    class="h-8 w-8 rounded-full"
                                >
                            </div>
                            <div class="flex-1">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                </div>
                                <div class="mt-1 text-sm text-gray-700">
                                    {{ $comment->content }}
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Novo Comentário -->
                <div class="mt-4">
                    <div class="flex">
                        <input 
                            type="text"
                            wire:model="newCommentContent"
                            wire:keydown.enter="addComment"
                            placeholder="Adicionar comentário..."
                            class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <button
                            wire:click="addComment"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Comentar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Anexos -->
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Anexos</h4>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($attachments as $attachment)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                            <div class="flex items-center">
                                @if(Str::endsWith($attachment->filename, ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img 
                                        src="{{ Storage::url($attachment->file_path) }}"
                                        alt="{{ $attachment->filename }}"
                                        class="h-10 w-10 object-cover rounded"
                                    >
                                @else
                                    <svg class="h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $attachment->filename }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Por {{ $attachment->uploader->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a 
                                    href="{{ Storage::url($attachment->file_path) }}"
                                    target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                                <button
                                    wire:click="deleteAttachment({{ $attachment->id }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Upload de Anexos -->
                <div class="mt-4">
                    <div
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                    >
                        <label class="block">
                            <span class="sr-only">Escolher arquivos</span>
                            <input 
                                type="file" 
                                wire:model="attachments" 
                                multiple
                                class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                            >
                        </label>

                        <!-- Progress Bar -->
                        <div x-show="isUploading" class="mt-2">
                            <div class="h-2 bg-indigo-100 rounded-full">
                                <div
                                    class="h-2 bg-indigo-600 rounded-full transition-all"
                                    x-bind:style="`width: ${progress}%`"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dependências -->
            @if($dependencies->isNotEmpty())
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Dependências</h4>
                    <div class="space-y-2">
                        @foreach($dependencies as $dependency)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                <span class="text-sm {{ $dependency->status === 'completed' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $dependency->title }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $dependency->status === 'completed' ? 'Concluída' : 'Pendente' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Botões de Ação (Modo de Edição) -->
    @if($editMode)
        <div class="mt-4 flex justify-end space-x-3">
            <button
                wire:click="toggleEditMode"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Cancelar
            </button>
            <button
                wire:click="updateTask"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Salvar
            </button>
        </div>
    @endif
</div> 