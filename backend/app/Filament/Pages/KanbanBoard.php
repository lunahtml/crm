<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Task;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Facades\Auth;

class KanbanBoard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static string $view = 'filament.pages.kanban-board';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 2;

    public $selectedProject = null;
    public $tasks = [];

    public function mount(): void
    {
        $this->selectedProject = Project::first()?->id;
        $this->loadTasks();
    }

    public function getProjects()
    {
        return Project::all();
    }

    public function loadTasks(): void
    {
        if ($this->selectedProject) {
            $this->tasks = Task::where('project_id', $this->selectedProject)
                ->with(['assignee', 'epic'])
                ->orderBy('status')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'epic' => $task->epic ? [
                            'id' => $task->epic->id,
                            'name' => $task->epic->name
                        ] : null,
                        'assignee' => $task->assignee ? [
                            'id' => $task->assignee->id,
                            'name' => $task->assignee->name
                        ] : null,
                        'hours_estimated' => $task->hours_estimated
                    ];
                })
                ->groupBy('status')
                ->toArray();
        } else {
            $this->tasks = [];
        }
    }

    public function updatedSelectedProject(): void
    {
        $this->loadTasks();
    }

    public function updateTaskStatus($taskId, $newStatus)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->status = $newStatus;
            $task->save();
            $this->loadTasks();
        }
    }

    public function createTask()
    {
        return redirect()->route('filament.admin.resources.tasks.create', ['project_id' => $this->selectedProject]);
    }
}