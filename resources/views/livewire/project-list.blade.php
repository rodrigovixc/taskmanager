<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Projetos') }}
            </h2>
            <button wire:click="$set('showCreateModal', true)" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Novo Projeto
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Busca -->
            <div class="mb-6">
                <input type="text" wire:model.live="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Buscar projetos...">
            </div>

            <!-- Grid de Projetos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $project->description }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="editProject({{ $project->id }})" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteProject({{ $project->id }})" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $project->color }}20; color: {{ $project->color }}">
                                    {{ $project->tasks_count }} {{ $project->tasks_count == 1 ? 'tarefa' : 'tarefas' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-gray-500">
                        Nenhum projeto encontrado.
                    </div>
                @endforelse
            </div>

            <!-- Paginação -->
            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de Criação/Edição -->
    <x-dialog-modal wire:model.live="showCreateModal">
        <x-slot name="title">
            {{ isset($form['id']) ? 'Editar Projeto' : 'Novo Projeto' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="name" value="Nome" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model="form.name" />
                    @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label for="description" value="Descrição" />
                    <x-textarea id="description" class="mt-1 block w-full" wire:model="form.description" />
                    @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label for="color" value="Cor" />
                    <x-input id="color" type="color" class="mt-1 block w-full" wire:model="form.color" />
                    @error('form.color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCreateModal', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-button class="ml-3" wire:click="createProject" wire:loading.attr="disabled">
                {{ isset($form['id']) ? 'Salvar' : 'Criar' }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div> 