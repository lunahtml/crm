<div class="p-4">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Kanban доска</h2>
        
        <div class="w-64">
            <select 
                wire:model.live="selectedProject" 
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
                <option value="">Выберите проект</option>
                @foreach($this->getProjects() as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedProject)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4" x-data="kanbanBoard()" x-init="init($wire)">
            <!-- To Do -->
            <div class="bg-gray-50 rounded-lg p-3">
                <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                    To Do
                    <span class="ml-auto bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs">
                        {{ count($tasks['todo'] ?? []) }}
                    </span>
                </h3>
                
                <div class="space-y-2 min-h-[200px]"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event, 'todo')">
                    
                    @foreach(($tasks['todo'] ?? []) as $task)
                        <div class="bg-white rounded-lg shadow-sm p-3 cursor-move border-l-4 border-gray-300"
                             draggable="true"
                             @dragstart="handleDragStart($event, {{ $task['id'] }})"
                             data-task-id="{{ $task['id'] }}">
                            <h4 class="font-medium text-gray-800">{{ $task['title'] }}</h4>
                            @if($task['assignee'] ?? null)
                                <p class="text-xs text-gray-500 mt-1">
                                    👤 {{ $task['assignee']['name'] }}
                                </p>
                            @endif
                            @if($task['hours_estimated'] ?? null)
                                <p class="text-xs text-gray-400 mt-1">
                                    ⏱ {{ $task['hours_estimated'] }}h
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-yellow-50 rounded-lg p-3">
                <h3 class="font-semibold text-yellow-700 mb-3 flex items-center">
                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                    In Progress
                    <span class="ml-auto bg-yellow-200 text-yellow-600 px-2 py-0.5 rounded-full text-xs">
                        {{ count($tasks['in_progress'] ?? []) }}
                    </span>
                </h3>
                
                <div class="space-y-2 min-h-[200px]"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event, 'in_progress')">
                    
                    @foreach(($tasks['in_progress'] ?? []) as $task)
                        <div class="bg-white rounded-lg shadow-sm p-3 cursor-move border-l-4 border-yellow-400"
                             draggable="true"
                             @dragstart="handleDragStart($event, {{ $task['id'] }})"
                             data-task-id="{{ $task['id'] }}">
                            <h4 class="font-medium text-gray-800">{{ $task['title'] }}</h4>
                            @if($task['assignee'] ?? null)
                                <p class="text-xs text-gray-500 mt-1">
                                    👤 {{ $task['assignee']['name'] }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Review -->
            <div class="bg-blue-50 rounded-lg p-3">
                <h3 class="font-semibold text-blue-700 mb-3 flex items-center">
                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                    Review
                    <span class="ml-auto bg-blue-200 text-blue-600 px-2 py-0.5 rounded-full text-xs">
                        {{ count($tasks['review'] ?? []) }}
                    </span>
                </h3>
                
                <div class="space-y-2 min-h-[200px]"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event, 'review')">
                    
                    @foreach(($tasks['review'] ?? []) as $task)
                        <div class="bg-white rounded-lg shadow-sm p-3 cursor-move border-l-4 border-blue-400"
                             draggable="true"
                             @dragstart="handleDragStart($event, {{ $task['id'] }})"
                             data-task-id="{{ $task['id'] }}">
                            <h4 class="font-medium text-gray-800">{{ $task['title'] }}</h4>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-green-50 rounded-lg p-3">
                <h3 class="font-semibold text-green-700 mb-3 flex items-center">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                    Completed
                    <span class="ml-auto bg-green-200 text-green-600 px-2 py-0.5 rounded-full text-xs">
                        {{ count($tasks['completed'] ?? []) }}
                    </span>
                </h3>
                
                <div class="space-y-2 min-h-[200px]"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event, 'completed')">
                    
                    @foreach(($tasks['completed'] ?? []) as $task)
                        <div class="bg-white rounded-lg shadow-sm p-3 cursor-move border-l-4 border-green-400 opacity-75"
                             draggable="true"
                             @dragstart="handleDragStart($event, {{ $task['id'] }})"
                             data-task-id="{{ $task['id'] }}">
                            <h4 class="font-medium text-gray-600 line-through">{{ $task['title'] }}</h4>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500">Выберите проект для просмотра Kanban доски</p>
        </div>
    @endif
</div>

<script>
    function kanbanBoard() {
        return {
            draggedTaskId: null,
            
            init($wire) {
                this.$wire = $wire;
            },
            
            handleDragStart(event, taskId) {
                this.draggedTaskId = taskId;
                event.dataTransfer.effectAllowed = 'move';
            },
            
            handleDrop(event, newStatus) {
                if (this.draggedTaskId) {
                    this.$wire.updateTaskStatus(this.draggedTaskId, newStatus);
                    this.draggedTaskId = null;
                }
            }
        }
    }
</script>

<style>
    [draggable=true] {
        user-select: none;
        -webkit-user-drag: element;
    }
    
    [draggable=true]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
    }
</style>