<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Cabeçalho do Calendário -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 rounded-full">
                                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <h3 class="text-lg font-semibold text-gray-900 mx-4">
                                {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth)->translatedFormat('F Y') }}
                            </h3>
                            <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-full">
                                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Grade do Calendário -->
                    <div class="grid grid-cols-7 gap-px bg-gray-200">
                        <!-- Cabeçalho dos Dias da Semana -->
                        @foreach(['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'] as $dayName)
                            <div class="bg-gray-50 py-2 text-center text-sm font-semibold text-gray-900">
                                {{ $dayName }}
                            </div>
                        @endforeach

                        <!-- Dias do Calendário -->
                        @foreach($this->calendarDays() as $day)
                            <div 
                                wire:click="selectDate('{{ $day['date'] }}')"
                                class="min-h-[120px] bg-white p-2 {{ $day['isToday'] ? 'bg-blue-50' : '' }} {{ $day['isCurrentMonth'] ? '' : 'bg-gray-50' }} {{ $selectedDate === $day['date'] ? 'ring-2 ring-blue-500' : '' }} hover:bg-gray-50 cursor-pointer"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-sm {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ \Carbon\Carbon::parse($day['date'])->format('j') }}
                                    </span>
                                    @if(isset($tasks[$day['date']]) && count($tasks[$day['date']]) > 0)
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-blue-500 rounded-full">
                                            {{ count($tasks[$day['date']]) }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if(isset($tasks[$day['date']]))
                                    <div class="mt-1 space-y-1">
                                        @foreach($tasks[$day['date']] as $task)
                                            <div class="text-xs p-1 rounded truncate {{ 
                                                $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                            }}">
                                                {{ $task->title }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Modal de Tarefas do Dia -->
            @if($selectedDate)
                <div class="mt-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Tarefas para {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \de F \de Y') }}
                                </h3>
                                <button wire:click="$set('selectedDate', null)" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            @if(isset($tasks[$selectedDate]) && count($tasks[$selectedDate]) > 0)
                                <div class="space-y-4">
                                    @foreach($tasks[$selectedDate] as $task)
                                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ $task->title }}</h4>
                                                    @if($task->project)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                            style="background-color: {{ $task->project->color }}20; color: {{ $task->project->color }}">
                                                            {{ $task->project->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($task->description)
                                                    <p class="mt-1 text-sm text-gray-500">{{ $task->description }}</p>
                                                @endif
                                                <div class="mt-2 flex items-center space-x-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                        $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                        ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                                    }}">
                                                        {{ __($task->status) }}
                                                    </span>
                                                    @if($task->subtasks_count > 0)
                                                        <span class="text-sm text-gray-500">
                                                            {{ $task->completed_subtasks_count }}/{{ $task->subtasks_count }} subtarefas
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-gray-500 py-4">
                                    Nenhuma tarefa para este dia.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> 