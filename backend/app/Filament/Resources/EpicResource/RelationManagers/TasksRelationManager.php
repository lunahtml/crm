<?php

namespace App\Filament\Resources\EpicResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                Tables\Filters\SelectFilter::make('assignee')
                    ->relationship('assignee', 'name'),
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
                
                // Прикрепить существующую задачу
                Tables\Actions\AttachAction::make()
                    ->label('Добавить существующую')
                    ->icon('heroicon-o-link')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        return $query
                            ->where('project_id', $this->getOwnerRecord()->project_id)
                            ->whereNull('epic_id')
                            ->orWhere('epic_id', $this->getOwnerRecord()->id);
                    })
                    ->recordSelectSearchColumns(['title'])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['project_id'] = $this->getOwnerRecord()->project_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make() // Открепить от эпика
                    ->label('Убрать из эпика'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Убрать из эпика'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}