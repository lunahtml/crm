<x-filament-panels::page>
    <style>
        /* Принудительные стили для канбана */
        .kanban-container {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 1rem !important;
        }
        
        @media (max-width: 768px) {
            .kanban-container {
                grid-template-columns: 1fr !important;
            }
        }

        [draggable=true] {
            user-select: none;
            -webkit-user-drag: element;
        }
        
        [draggable=true]:active {
            opacity: 0.5;
            cursor: grabbing;
        }
        
        .bg-gray-50 [draggable=true]:hover,
        .bg-yellow-50 [draggable=true]:hover,
        .bg-blue-50 [draggable=true]:hover,
        .bg-green-50 [draggable=true]:hover {
            transform: translateY(-2px);
        }
        select, select option {
    color: #1f2937 !important;
    background-color: #ffffff !important;
}

select option:checked {
    background-color: #e5e7eb !important;
    color: #1f2937 !important;
}
    </style>

<div class="space-y-6" x-data="kanbanBoard" x-init="init()">
        <!-- Header с выбором проекта -->
        <div class="flex items-center justify-between bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <h2 class="text-xl font-bold text-gray-800">Kanban доска</h2>
                
                <div class="w-72">
                <select 
    wire:model.live="selectedProject" 
    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
    style="color: #1f2937; background-color: white;"
>
    <option value="" style="color: #6b7280;">Выберите проект</option>
    @foreach($this->getProjects() as $project)
        <option value="{{ $project->id }}" style="color: #1f2937;">{{ $project->name }}</option>
    @endforeach
</select>
                </div>
            </div>
            
            @if($selectedProject)
                <button 
                    wire:click="createTask"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Новая задача
                </button>
            @endif
        </div>

        @if($selectedProject)
            <!-- Колонки канбана -->
            <div class="kanban-container min-h-[600px]">
                
                <!-- To Do -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-700 flex items-center">
                            <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                            To Do
                        </h3>
                        <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded-full text-xs">
                            {{ count($tasks['todo'] ?? []) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 min-h-[400px]"
                         @dragover.prevent
                         @drop.prevent="handleDrop($event, 'todo')">
                        
                        @foreach(($tasks['todo'] ?? []) as $task)
                            <div class="bg-white rounded-lg shadow-sm p-4 cursor-move border-l-4 border-gray-300 hover:shadow-md transition-all"
                                 draggable="true"
                                 @dragstart="handleDragStart($event, {{ $task['id'] }})"
                                 data-task-id="{{ $task['id'] }}"
                                 @click="openTask({{ $task['id'] }})">
                                <h4 class="font-medium text-gray-800">{{ $task['title'] }}</h4>
                                
                                @if($task['description'] ?? null)
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $task['description'] }}</p>
                                @endif
                                
                                @if($task['epic'] ?? null)
                                    <p class="text-xs text-indigo-600 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                        {{ $task['epic']['name'] }}
                                    </p>
                                @endif
                                
                                @if($task['assignee'] ?? null)
                                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $task['assignee']['name'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- In Progress -->
                <div class="bg-yellow-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-yellow-700 flex items-center">
                            <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                            In Progress
                        </h3>
                        <span class="bg-yellow-200 text-yellow-600 px-2 py-1 rounded-full text-xs">
                            {{ count($tasks['in_progress'] ?? []) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 min-h-[400px]"
                         @dragover.prevent
                         @drop.prevent="handleDrop($event, 'in_progress')">
                        
                        @foreach(($tasks['in_progress'] ?? []) as $task)
                            <div class="bg-white rounded-lg shadow-sm p-4 cursor-move border-l-4 border-yellow-400 hover:shadow-md transition-all"
                                 draggable="true"
                                 @dragstart="handleDragStart($event, {{ $task['id'] }})"
                                 data-task-id="{{ $task['id'] }}"
                                 @click="openTask({{ $task['id'] }})">
                                <h4 class="font-medium text-gray-800">{{ $task['title'] }}</h4>
                                
                                @if($task['description'] ?? null)
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $task['description'] }}</p>
                                @endif
                                
                                @if($task['epic'] ?? null)
                                    <p class="text-xs text-indigo-600 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                        {{ $task['epic']['name'] }}
                                    </p>
                                @endif
                                
                                @if($task['assignee'] ?? null)
                                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $task['assignee']['name'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Review -->
                <div class="bg-blue-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-blue-700 flex items-center">
                            <span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                            Review
                        </h3>
                        <span class="bg-blue-200 text-blue-600 px-2 py-1 rounded-full text-xs">
                            {{ count($tasks['review'] ?? []) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 min-h-[400px]"
                         @dragover.prevent
                         @drop.prevent="handleDrop($event, 'review')">
                        
                        @foreach(($tasks['review'] ?? []) as $task)
                            <div class="bg-white rounded-lg shadow-sm p-4 cursor-move border-l-4 border-blue-400 hover:shadow-md transition-all"
                                 draggable="true"
                                 @dragstart="handleDragStart($event, {{ $task['id'] }})"
                                 data-task-id="{{ $task['id'] }}"
                                 @click="openTask({{ $task['id'] }})">
                                <h4 class="font-medium text-gray-800">{{ $task['title'] }}</h4>
                                
                                @if($task['description'] ?? null)
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $task['description'] }}</p>
                                @endif
                                
                                @if($task['epic'] ?? null)
                                    <p class="text-xs text-indigo-600 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                        {{ $task['epic']['name'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Completed -->
                <div class="bg-green-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-green-700 flex items-center">
                            <span class="w-3 h-3 bg-green-400 rounded-full mr-2"></span>
                            Completed
                        </h3>
                        <span class="bg-green-200 text-green-600 px-2 py-1 rounded-full text-xs">
                            {{ count($tasks['completed'] ?? []) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 min-h-[400px]"
                         @dragover.prevent
                         @drop.prevent="handleDrop($event, 'completed')">
                        
                        @foreach(($tasks['completed'] ?? []) as $task)
                            <div class="bg-white rounded-lg shadow-sm p-4 cursor-move border-l-4 border-green-400 opacity-75 hover:shadow-md transition-all"
                                 draggable="true"
                                 @dragstart="handleDragStart($event, {{ $task['id'] }})"
                                 data-task-id="{{ $task['id'] }}"
                                 @click="openTask({{ $task['id'] }})">
                                <h4 class="font-medium text-gray-600 line-through">{{ $task['title'] }}</h4>
                                
                                @if($task['epic'] ?? null)
                                    <p class="text-xs text-indigo-600 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                        {{ $task['epic']['name'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Нет выбранного проекта</h3>
                <p class="text-gray-500">Выберите проект для просмотра Kanban доски</p>
            </div>
        @endif
    </div>

    <!-- <script>
        window.kanbanBoard = function() {
            return {
                draggedTaskId: null,
                
                init($wire) {
                    this.$wire = $wire;
                    console.log('Kanban initialized');
                },
                
                handleDragStart(event, taskId) {
                    this.draggedTaskId = taskId;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', taskId);
                    console.log('Drag started:', taskId);
                },
                
                handleDrop(event, newStatus) {
                    event.preventDefault();
                    if (this.draggedTaskId) {
                        console.log('Drop:', this.draggedTaskId, 'to', newStatus);
                        this.$wire.updateTaskStatus(this.draggedTaskId, newStatus);
                        this.draggedTaskId = null;
                    }
                },
                
                openTask(taskId) {
                    window.location.href = `/admin/tasks/${taskId}/edit`;
                }
            }
        }
    </script> -->
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kanbanBoard', () => ({
            draggedTaskId: null,
            
            init() {
                this.$wire = this.$wire || window.Livewire.find(this.$el.closest('[wire:id]').getAttribute('wire:id'));
                console.log('Kanban initialized');
            },
            
            handleDragStart(event, taskId) {
                this.draggedTaskId = taskId;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', taskId);
                console.log('Drag started:', taskId);
            },
            
            handleDrop(event, newStatus) {
                event.preventDefault();
                if (this.draggedTaskId) {
                    console.log('Drop:', this.draggedTaskId, 'to', newStatus);
                    if (this.$wire) {
                        this.$wire.updateTaskStatus(this.draggedTaskId, newStatus);
                    }
                    this.draggedTaskId = null;
                }
            },
            
            openTask(taskId) {
                window.location.href = `/admin/tasks/${taskId}/edit`;
            }
        }));
    });
</script>
</x-filament-panels::page>