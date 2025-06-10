<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RvOrderResource\Pages;
use App\Filament\Resources\RvOrderResource\RelationManagers;
use App\Models\RvOrder;
use App\Enums\OrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RvOrderResource extends Resource
{
    protected static ?string $model = RvOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '互动管理';

    protected static ?string $navigationLabel = '房车订单';

    protected static ?string $modelLabel = '房车订单';

    protected static ?string $pluralModelLabel = '房车订单';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('order_no')
                            ->label('订单编号')
                            ->required()
                            ->disabledOn('edit') // 编辑时不允许修改
                            ->maxLength(255),

                        // 使用 Select 组件并关联模型，显示用户名称而非ID
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('预订用户')
                            ->searchable()
                            ->disabledOn('edit')
                            ->native(false)
                            ->required(),

                        // 同样，关联显示房车名称
                        Forms\Components\Select::make('rv_id')
                            ->relationship('rv', 'name')
                            ->label('预订房车')
                            ->searchable()
                            ->disabledOn('edit')
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('deposit_amount')
                            ->label('预付定金')
                            ->required()
                            ->numeric()
                            ->prefix('¥'),

                        Forms\Components\Select::make('status')
                            ->label('订单状态')
                            ->options(OrderStatus::class)
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('created_at')
                            ->disabledOn('edit')
                            ->label('下单时间'),
                    ]), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label('订单编号')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('订单号已复制'),

                Tables\Columns\TextColumn::make('rv.name')
                    ->label('预订房车')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('预订用户')
                    ->limit(15)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deposit_amount')
                    ->label('定金金额')
                    ->money('CNY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('订单状态')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('下单时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('订单状态')
                    ->options(OrderStatus::class)
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRvOrders::route('/'),
            'create' => Pages\CreateRvOrder::route('/create'),
            'edit' => Pages\EditRvOrder::route('/{record}/edit'),
        ];
    }
}