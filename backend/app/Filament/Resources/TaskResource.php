<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\TimeEntriesRelationManager;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Task Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Select::make('project_id')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('assignee_id')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'todo' => 'To Do',
                                'in_progress' => 'In Progress',
                                'review' => 'Review',
                                'completed' => 'Completed',
                            ])
                            ->default('todo')
                            ->required(),
                        TextInput::make('hours_estimated')
                            ->numeric()
                            ->suffix('hours')
                            ->step(0.5)
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'todo',
                        'warning' => 'in_progress',
                        'info' => 'review',
                        'success' => 'completed',
                    ]),
                TextColumn::make('hours_estimated')
                    ->suffix('h')
                    ->sortable(),
                TextColumn::make('time_entries_sum_hours')
                    ->sum('timeEntries', 'hours')
                    ->label('Logged')
                    ->suffix('h')
                    ->badge()
                    ->color('info'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'todo' => 'To Do',
                        'in_progress' => 'In Progress',
                        'review' => 'Review',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TimeEntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    protected function afterCreate($record): void
{
    if ($record->assignee_id) {
        broadcast(new \App\Events\TaskAssigned($record));
    }
}
}