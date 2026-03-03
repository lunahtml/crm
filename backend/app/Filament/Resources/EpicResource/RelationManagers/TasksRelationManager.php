<?php

namespace App\Filament\Resources\EpicResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    
    protected static ?string $title = 'Задачи эпика';
    
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('assignee_id')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'todo' => 'To Do',
                        'in_progress' => 'In Progress',
                        'review' => 'Review',
                        'completed' => 'Completed',
                    ])
                    ->default('todo'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('hours_estimated')
                    ->numeric()
                    ->suffix('h'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'review' => 'info',
                        'completed' => 'success',
                    }),
                Tables\Columns\TextColumn::make('hours_estimated')
                    ->suffix('h'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'todo' => 'To Do',
                        'in_progress' => 'In Progress',
                        'review' => 'Review',
                        'completed' => 'Completed',
                    ]),
            ])
            ->headerActions([
                // Создать новую задачу
                Tables\Actions\CreateAction::make()
                    ->label('Новая задача')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['project_id'] = $this->getOwnerRecord()->project_id;
                        return $data;
                    }),
                
                // Кастомное действие для добавления существующих задач
                Tables\Actions\Action::make('attachTasks')
                    ->label('Добавить существующие задачи')
                    ->icon('heroicon-o-link')
                    ->form([
                        Forms\Components\Select::make('task_ids')
                            ->label('Выберите задачи')
                            ->multiple()
                            ->options(function () {
                                $projectId = $this->getOwnerRecord()->project_id;
                                $epicId = $this->getOwnerRecord()->id;
                                
                                return \App\Models\Task::where('project_id', $projectId)
                                    ->where(function ($query) use ($epicId) {
                                        $query->whereNull('epic_id')
                                              ->orWhere('epic_id', $epicId);
                                    })
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $epic = $this->getOwnerRecord();
                        \App\Models\Task::whereIn('id', $data['task_ids'])
                            ->update(['epic_id' => $epic->id]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('detach')
                    ->label('Убрать из эпика')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Model $record): void {
                        $record->update(['epic_id' => null]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('detach')
                        ->label('Убрать из эпика')
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['epic_id' => null]);
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}