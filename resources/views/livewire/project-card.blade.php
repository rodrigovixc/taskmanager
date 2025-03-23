<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full" style="background-color: {{ $project->color }}"></div>
                <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
            </div>
            <div class="flex items-center space-x-2">
                <button 
                    wire:click="$dispatch('openEditModal', { projectId: {{ $project->id }} })"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button 
                    wire:click="$dispatch('openDeleteModal', { projectId: {{ $project->id }} })"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        @if($project->description)
            <p class="mt-2 text-sm text-gray-500">
                {{ $project->description }}
            </p>
        @endif

        <div class="mt-6">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>Progresso</span>
                <span>{{ $project->tasks_count > 0 ? round(($project->completed_tasks / $project->tasks_count) * 100) : 0 }}%</span>
            </div>
            <div class="mt-2 relative">
                <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                    <div
                        style="width: {{ $project->tasks_count > 0 ? ($project->completed_tasks / $project->tasks_count) * 100 : 0 }}%"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500"
                    ></div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between text-sm">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-gray-500">{{ $project->tasks_count }} tarefas</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-gray-500">{{ $project->completed_tasks }} concluídas</span>
            </div>
        </div>

        <div class="mt-6">
            <a 
                href="{{ route('projects.show', $project) }}" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Ver Detalhes
            </a>
        </div>
    </div>
</div> 