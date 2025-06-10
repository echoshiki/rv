<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RvOrderResource\Pages;
use App\Filament\Resources\RvOrderResource\RelationManagers;
// use App\Filament\Resources\RvOrderResource\RelationManagers\PaymentsRelationManager;
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
                Forms\Components\Section::make('订单基础信息')
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
                            ->native(false)
                            ->required(),

                        // 同样，关联显示房车名称
                        Forms\Components\Select::make('rv_id')
                            ->relationship('rv', 'name')
                            ->label('预订房车')
                            ->searchable()
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('deposit_amount')
                            ->label('预付定金')
                            ->required()
                            ->numeric()
                            ->prefix('¥'),
                    ]),

                Forms\Components\Section::make('订单状态')
                    ->schema([
                        // 使用我们之前创建的 OrderStatus 枚举来填充选项
                        Forms\Components\Select::make('status')
                            ->label('订单状态')
                            ->options(OrderStatus::class) // Filament v3 可以直接使用枚举类
                            ->required()
                            ->native(false),
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
                    ->copyable() // 增加一键复制功能
                    ->copyMessage('订单号已复制'),

                // 显示关联模型的字段
                Tables\Columns\TextColumn::make('rv.name')
                    ->label('预订房车')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('预订用户')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deposit_amount')
                    ->label('定金金额')
                    ->money('CNY') // 使用 money() 方法自动格式化为货币
                    ->sortable(),
                
                // ✅ 使用 BadgeColumn 并结合枚举来优雅地显示状态
                Tables\Columns\TextColumn::make('status')
                    ->label('订单状态')
                    ->badge() // 使用徽章样式，更醒目
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::Pending => 'warning',
                        OrderStatus::Paid => 'success',
                        OrderStatus::Cancelled => 'gray',
                    })
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label()), // 直接调用枚举的 label() 方法

                Tables\Columns\TextColumn::make('created_at')
                    ->label('下单时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // 默认按创建时间倒序
            ->filters([
                // ✅ 添加一个强大的状态筛选器
                Tables\Filters\SelectFilter::make('status')
                    ->label('订单状态')
                    ->options(OrderStatus::class) // Filament v3 可以直接使用枚举类
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
            // ✅ 步骤二：注册我们下面将要创建的关联管理器
            // RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRvOrders::route('/'),
            'create' => Pages\CreateRvOrder::route('/create'),
            // 查看页面使用我们自定义的页面，以展示关联信息
            // 'view' => Pages\ViewRvOrder::route('/{record}'),
            'edit' => Pages\EditRvOrder::route('/{record}/edit'),
        ];
    }
}