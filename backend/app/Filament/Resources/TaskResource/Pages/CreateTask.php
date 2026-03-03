<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Events\TaskAssigned;
use Illuminate\Support\Facades\Log;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
    
    // Добавляем метод afterCreate
    protected function afterCreate(): void
    {
        $task = $this->record;
        
        Log::info('Task created in CreateTask', [
            'task_id' => $task->id,
            'assignee_id' => $task->assignee_id
        ]);
        
        if ($task->assignee_id) {
            Log::info('Dispatching TaskAssigned event from CreateTask');
            event(new TaskAssigned($task));
        }
    }
    protected function getRedirectUrl(): string
{
    // Если пришли с канбана, вернуться обратно
    if (request()->has('project_id')) {
        return route('filament.admin.pages.kanban-board');
    }
    
    return $this->getResource()::getUrl('index');
}
}
