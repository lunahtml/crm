<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Events\TaskAssigned;
use Illuminate\Support\Facades\Log;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    // Для массовых действий
    protected function getTableBulkActions(): array
    {
        return [
            Actions\BulkAction::make('assign')
                ->label('Assign to')
                ->action(function ($records, array $data) {
                    foreach ($records as $record) {
                        $oldAssignee = $record->assignee_id;
                        $record->assignee_id = $data['assignee_id'];
                        $record->save();
                        
                        if ($record->assignee_id && $oldAssignee !== $record->assignee_id) {
                            event(new TaskAssigned($record));
                        }
                    }
                })
                ->form([
                    \Filament\Forms\Components\Select::make('assignee_id')
                        ->relationship('assignee', 'name')
                        ->required(),
                ])
                ->deselectRecordsAfterCompletion(),
        ];
    }
}