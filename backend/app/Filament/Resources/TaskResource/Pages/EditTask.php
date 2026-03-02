<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Events\TaskAssigned; // Добавляем импорт
use Illuminate\Support\Facades\Log; // Для отладки

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    // Добавляем метод afterSave для проверки изменений
    protected function afterSave(): void
    {
        $task = $this->record;
        $original = $task->getOriginal('assignee_id');
        $current = $task->assignee_id;
        
        Log::info('Task saved in EditTask', [
            'task_id' => $task->id,
            'original_assignee' => $original,
            'new_assignee' => $current
        ]);
        
        // Отправляем событие, если назначили задачу кому-то
        if ($current && $original !== $current) {
            Log::info('Assignee changed, dispatching TaskAssigned event');
            event(new TaskAssigned($task));
        }
    }
}
