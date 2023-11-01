<?php

namespace Nurdaulet\FluxOrders\Filament\Resources;

use Nurdaulet\FluxOrders\Facades\StringFormatter;
use Nurdaulet\FluxOrders\Filament\Resources\OrderResource\Pages;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;
use Nurdaulet\FluxOrders\Models\City;
use Nurdaulet\FluxOrders\Models\Item;
use Nurdaulet\FluxOrders\Models\Order;
use Nurdaulet\FluxOrders\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $modelLabel = 'заказ';
    protected static ?string $pluralModelLabel = 'Заказы';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ad_id')
                    ->label(trans('admin.ad'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search) => Item::where('name', 'like', "%$search%")
                        ->limit(50)->selectRaw("id,  name")
                        ->pluck('name', 'id'))
                    ->getOptionLabelUsing(function ($value) {
                        return Item::find($value)?->name;
                    }),
                Forms\Components\Select::make('user_id')
                    ->label(trans('admin.user'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search) => User::whereRaw("concat(name, ' ', 'surname', ' ', 'company_name') like '%" . $search . "%'")
                        ->when(StringFormatter::onlyDigits($search), function ($query) use($search) {
                            $query->orWhere('phone', 'like', "%". StringFormatter::onlyDigits($search)."%");

                        })
                        ->limit(50)->selectRaw("id,   concat(name, ' ',surname, ' | ' , phone) as info")
                        ->pluck('info', 'id'))
                    ->getOptionLabelUsing(function ($value) {
                        $user = User::find($value);
                        return $user?->name . ' ' . $user?->surname . ' | ' . $user->phone;
                    }),
                Forms\Components\Select::make('receive_method_id')
                    ->relationship('receiveMethod', 'name')
                    ->preload()
                    ->label(trans('admin.receive_method')),
                Forms\Components\Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->preload()
                    ->label(trans('admin.payment_method')),
//                Forms\Components\TextInput::make('delivery_price')
//                    ->required()->label(trans('admin.delivery_price')),
                Forms\Components\DatePicker::make('delivery_date')
                    ->label(trans('admin.delivery_date')),
                Forms\Components\TextInput::make('delivery_time')
                    ->label(trans('admin.delivery_time')),
                Forms\Components\Toggle::make('is_fast_delivery')
                    ->label(trans('admin.is_fast_delivery')),
                Forms\Components\TextInput::make('rent_price')
                    ->required()->label(trans('admin.rent_price')),
                Forms\Components\TextInput::make('total_price')
                    ->required()->label(trans('admin.total_price')),
                Forms\Components\Select::make('status')
                    ->options(OrderHelper::STATUSES)
                    ->required()->label(trans('admin.status')),
                Forms\Components\Select::make('client_status')
                    ->options([
                        0 => 'Новый',
                        1 => 'Принял',
                        2 => 'Получил',
                        3 => 'Отменил',
                        4 => 'Завершен',
                    ])
                    ->required()->label(trans('admin.client_status')),
                Forms\Components\Select::make('client_status')
                    ->options([
                        0 => 'Новый',
                        1 => 'Принял',
                        2 => 'Отправил',
                        3 => 'Хочет обратно',
                        4 => 'Получил обратно',
                        5 => 'Отменил',
                    ])
                    ->required()->label(trans('admin.lord_status')),

                Forms\Components\Textarea::make('comment')
                    ->maxLength(65535)->label(trans('admin.comment')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label(trans('admin.id')),
                Tables\Columns\TextColumn::make('item.user.CompanyNameWithPhone')
                    ->url(fn(Order $record): string => $record->item?->user ? url("/users/{$record->item->user->id}/edit"  ) : '')
                    ->searchable()
                    ->label(trans('admin.company_name')),
//                Tables\Columns\TextColumn::make('user.full_name')
//                    ->label(trans('admin.user'))
//                    ->copyable(),
                Tables\Columns\TextColumn::make('user.FullNameWithPhone')
                    ->url(fn(Order $record): string => $record->user?->phone ? url('https://wa.me/+' . $record->user->phone) : 'Не заполнено')
                    ->searchable()
                    ->label(trans('admin.user'))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('item.name')
                    ->searchable()
                    ->label(trans('admin.ad'))
                    ->copyable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label(trans('admin.address'))
                    ->wrap()
                    ->toggleable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('delivery_date')
                    ->date()
                    ->label(trans('admin.delivery_date'))
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('receiveMethod.name')
                    ->label(trans('admin.receive_method'))
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label(trans('admin.payment_method'))
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('rent_price')
                    ->label(trans('admin.rent_price'))
                    ->toggleable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->toggleable()
                    ->label(trans('admin.total_price'))
                    ->copyable(),
                Tables\Columns\SelectColumn::make('platform')
                    ->options([
                        1 => 'android',
                        2 => 'ios',
                        3 => 'mobile',
                        4 => 'web'
                    ])
                    ->disabled()
                    ->toggleable()
                    ->label(trans('admin.platform')),

                Tables\Columns\TextColumn::make('city.name')
                    ->label(trans('admin.city'))
                    ->copyable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        0 => 'Новый',
                        1 => 'Принят',
                        2 => 'Активный',
                        4 => 'Отменен',
                        5 => 'Завершен',
                    ])
                    ->toggleable()
                    ->disabled()
                    ->label(trans('admin.status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->dateTime()->label(trans('admin.created_at'))->copyable(),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label(trans('admin.city'))
                    ->options(City::orderBy('name')->get()->pluck('name', 'id')->toArray()),
                SelectFilter::make('user_id')
                    ->label(trans('admin.user'))
                    ->options(User::orderBy('name')
                        ->whereNotNull('name')->get()
                        ->pluck('full_name_with_phone', 'id')
                        ->toArray()
                    ),

                SelectFilter::make('status')
                    ->label(trans('admin.status'))
                    ->options([
                            0 => 'Новый',
                            1 => 'Принят',
                            2 => 'Активный',
                            4 => 'Отменен',
                            5 => 'Завершен',
                        ]
                    ),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('с даты'),
                        Forms\Components\DatePicker::make('created_until')->label('до даты')->default(now()),
                    ])->query(function (Builder $query, array $data): Builder {

                        return $query
                            ->when(isset($data['created_from']), function ($query) use ($data) {
                                return $query->whereDate('created_at', '>=', $data['created_from']);
                            })
                            ->when(isset($data['created_until']), function ($query) use ($data) {
                                return $query->whereDate('created_at', '<=', $data['created_until']);
                            });
                    }),
//                Filter::make('phone')
//                    ->form([
//                        Forms\Components\TextInput::make('phone')->label('Номер заказчика'),
//                    ])
//                    ->query(fn (Builder $query, $data): Builder => $query->whereHas('user',function ($query) use($data) {
//                        return $query->where('phone','like',"%". $data['phone'] ."%");
//                    }))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'DESC');
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
