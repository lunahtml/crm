<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ProjectKanban extends Widget
{
    protected static string $view = 'filament.widgets.project-kanban';
    
    protected int | string | array $columnSpan = 'full';
    
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
                ->orderBy('status')
                ->orderBy('updated_at', 'desc')
                ->get()
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
    
    public function updateTaskStatus($taskId, $newStatus, $order = [])
    {
        $task = Task::find($taskId);
        if ($task && Auth::user()->can('update', $task)) {
            $task->status = $newStatus;
            $task->save();
            
            $this->loadTasks();
        }
    }
}