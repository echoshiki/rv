<?php

namespace App\Filament\Resources\RvOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'payment_no';

    protected static ?string $label = '支付记录';

    protected static ?string $title = '支付记录';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('out_trade_no')
                    ->label('交易单号')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('payment_gateway')
                    ->label('支付类型')
                    ->options(PaymentType::class) 
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('amount')
                    ->label('支付金额')
                    ->required()
                    ->numeric()
                    ->prefix('¥'),

                Forms\Components\Select::make('status')
                    ->label('支付状态')
                    ->options(PaymentStatus::class)
                    ->required()
                    ->native(false),

                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('支付时间')
                    ->required()
                    ->timezone('Asia/Shanghai')
                    ->displayFormat('Y-m-d H:i:s')
                    ->native(false),

                Forms\Components\TextInput::make('transaction_id')
                    ->label('微信交易号')
                    ->columnSpanFull(255),

                Forms\Components\Textarea::make('remark')
                    ->label('备注')
                    ->columnSpanFull()
                    ->maxLength(1000),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('out_trade_no')
                    ->label('支付单号')
                    ->copyable()
                    ->copyMessage('支付单号已复制'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('支付金额')
                    ->money('CNY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('支付状态')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 