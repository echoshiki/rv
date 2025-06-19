<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = '互动管理';

    protected static ?string $navigationLabel = '支付订单';

    protected static ?string $modelLabel = '支付订单';

    protected static ?string $pluralModelLabel = '支付订单';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('out_trade_no')
                    ->label('商户订单号')
                    ->required()
                    ->maxLength(64),
                Forms\Components\TextInput::make('transaction_id')
                    ->label('支付网关交易号')
                    ->maxLength(64),
                Forms\Components\TextInput::make('amount')
                    ->label('支付金额')
                    ->required()
                    ->numeric()
                    ->prefix('¥'),
                Forms\Components\Select::make('payment_gateway')
                    ->label('支付方式')
                    ->options(PaymentType::class)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('支付状态')
                    ->options(PaymentStatus::class)
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('支付时间'),
                Forms\Components\Select::make('user_id')
                    ->label('用户')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('payable_type')
                    ->label('业务类型')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'App\Models\RvOrder' => '房车订单',
                            'App\Models\ActivityRegistration' => '活动报名',
                            default => $state,
                        };
                    })
                    ->required(),
                Forms\Components\TextInput::make('payable_id')
                    ->label('业务ID')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('out_trade_no')
                    ->label('商户订单号')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('支付金额')
                    ->money('CNY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('支付状态')
                    ->badge(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('支付时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('电话')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payable_type')
                    ->label('业务类型')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'App\Models\RvOrder' => '房车订单',
                            'App\Models\ActivityRegistration' => '活动报名',
                            default => $state,
                        };
                    })
                    ->badge()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('支付状态')
                    ->options(PaymentStatus::class),
                Tables\Filters\SelectFilter::make('payment_gateway')
                    ->label('支付方式')
                    ->options(PaymentType::class),
                Tables\Filters\Filter::make('paid_at')
                    ->label('支付时间')
                    ->form([
                        Forms\Components\DatePicker::make('paid_from')
                            ->label('开始时间'),
                        Forms\Components\DatePicker::make('paid_until')
                            ->label('结束时间'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['paid_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('paid_at', '>=', $date),
                            )
                            ->when(
                                $data['paid_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('paid_at', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('amount')
                    ->label('支付金额')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('最小金额')
                            ->numeric(),
                        Forms\Components\TextInput::make('amount_to')
                            ->label('最大金额')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPayments::route('/'),
            // 'create' => Pages\CreatePayment::route('/create'),
            // 'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    // 禁止创建
    public static function canCreate(): bool
    {
        return false;
    }
}
