<?php

namespace Nurdaulet\FluxOrders\Filament\Resources;

use Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource\Pages;
use Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource\RelationManagers;
use Nurdaulet\FluxOrders\Models\CancelReason;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CancelReasonResource extends Resource
{
    protected static ?string $model = CancelReason::class;

    protected static ?string $navigationIcon = 'heroicon-o-pause';
    protected static ?string $modelLabel = 'причину';
    protected static ?string $pluralModelLabel = 'Причины отмены заказов';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->label(trans('admin.name')),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->label(trans('admin.description')),
                Forms\Components\Toggle::make('status')
                    ->required()
                    ->label(trans('admin.status')),
                Forms\Components\Select::make('type')
                    ->options([
                        0 => 'Клиент',
                        1 => 'Арендодатель',
                    ])
                    ->label('Тип'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('admin.name')),
                Tables\Columns\TextColumn::make('description')->label(trans('admin.description')),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()->label(trans('admin.status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->label(trans('admin.created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->label(trans('admin.updated_at')),
                Tables\Columns\BadgeColumn::make('type')
                    ->enum([
                        0 => 'Клиент',
                        1 => 'Арендодатель',
                    ])
                    ->label('Тип'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCancelReasons::route('/'),
            'create' => Pages\CreateCancelReason::route('/create'),
            'edit' => Pages\EditCancelReason::route('/{record}/edit'),
        ];
    }
}
