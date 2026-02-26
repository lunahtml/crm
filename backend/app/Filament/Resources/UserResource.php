<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->hiddenOn('edit')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Employment Details')
                    ->schema([
                        TextInput::make('position')
                            ->maxLength(255),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'project_owner' => 'Project Owner',
                                'backend' => 'Backend Developer',
                                'frontend' => 'Frontend Developer',
                                'designer' => 'Designer',
                                'employee' => 'Employee',
                            ])
                            ->default('employee'),
                        TextInput::make('hourly_rate')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        TextInput::make('salary')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        DatePicker::make('hired_at'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->searchable(),
                BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'project_owner',
                        'warning' => fn ($state): bool => in_array($state, ['backend', 'frontend']),
                        'info' => 'designer',
                        'gray' => 'employee',
                    ]),
                TextColumn::make('hourly_rate')
                    ->money('USD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('salary')
                    ->money('USD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hired_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'project_owner' => 'Project Owner',
                        'backend' => 'Backend Developer',
                        'frontend' => 'Frontend Developer',
                        'designer' => 'Designer',
                        'employee' => 'Employee',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}