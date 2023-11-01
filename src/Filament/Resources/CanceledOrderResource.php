<?php

namespace Nurdaulet\FluxOrders\Filament\Resources;

use Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource\Pages;
use Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource\RelationManagers;
use Nurdaulet\FluxOrders\Models\CanceledOrder;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CanceledOrderResource extends Resource
{
    protected static ?string $model = CanceledOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $modelLabel = 'заказ';
    protected static ?string $pluralModelLabel = 'Отмененные заказы';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->preload()
                    ->label(trans('admin.order')),
                Forms\Components\Select::make('cancel_id')
                    ->relationship('reason', 'name')
                    ->preload()
                    ->label(trans('admin.cancel_reason')),
                Forms\Components\Textarea::make('comment')
                    ->label(trans('admin.comment')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label(trans('admin.order')),
                Tables\Columns\TextColumn::make('reason.name')
                    ->label(trans('admin.cancel_reason')),
                Tables\Columns\TextColumn::make('comment')
                    ->label(trans('admin.comment')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->label(trans('admin.created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->label(trans('admin.updated_at')),
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
            'index' => Pages\ListCanceledOrders::route('/'),
            'create' => Pages\CreateCanceledOrder::route('/create'),
            'edit' => Pages\EditCanceledOrder::route('/{record}/edit'),
        ];
    }
}
