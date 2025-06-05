<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointLogResource\Pages;
// use App\Filament\Resources\PointLogResource\RelationManagers; // 如果有，可以保留或删除
use App\Models\PointLog;
use App\Models\User; // 引入 User 模型用于关联选择
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope; // 如果模型使用软删除
use Illuminate\Support\Facades\Auth;

class PointLogResource extends Resource
{
    protected static ?string $model = PointLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list'; // 选择一个合适的图标

    protected static ?string $navigationGroup = '用户管理'; // 或者 '日志管理'

    protected static ?string $modelLabel = '积分记录'; // 单数标签

    protected static ?string $pluralModelLabel = '积分记录'; // 复数标签

    protected static ?string $slug = 'point-logs';
    
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return '积分记录';
    }

    // 通常积分记录是系统生成的，不建议手动创建或修改核心数据。
    // 如果确实需要手动创建/编辑，以下 form() 方法可以启用。
    // 否则，可以考虑移除 CreatePointLog 和 EditPointLog 页面，并简化 form()。
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name') // 关联 User 模型，显示 name 字段
                    ->searchable()
                    ->preload() // 预加载选项，适用于用户不多的情况
                    ->label('用户')
                    ->required()
                    ->columnSpanFull()
                    // 创建时通常不可选，因为是系统记录；若要手动创建，则需要
                    ->disabled(fn (string $operation): bool => $operation !== 'create'),

                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'name') // 关联 User 模型 (作为 admin)，显示 name 字段
                    ->searchable()
                    ->preload()
                    ->label('操作管理员')
                    ->default(fn () => Auth::id()) // 默认当前管理员
                    // 通常不可编辑，记录当时操作员
                    ->disabled(fn (string $operation): bool => $operation !== 'create'),

                Forms\Components\Select::make('type')
                    ->label('操作类型')
                    ->options([
                        'increase' => '增加',
                        'decrease' => '减少',
                        'reset' => '重置',
                    ])
                    ->required()
                    ->native(false)
                    ->disabled(fn (string $operation): bool => $operation !== 'create'),

                Forms\Components\TextInput::make('amount')
                    ->label('变动值/重置值')
                    ->numeric()
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation !== 'create'),

                Forms\Components\TextInput::make('points_before')
                    ->label('操作前积分')
                    ->numeric()
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation !== 'create'),

                Forms\Components\TextInput::make('points_after')
                    ->label('操作后积分')
                    ->numeric()
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation !== 'create'),

                Forms\Components\Textarea::make('remarks')
                    ->label('备注')
                    ->columnSpanFull()
                    // 备注通常可以编辑
                    // ->disabled(fn (string $operation): bool => $operation !== 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('用户')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('操作管理员')
                    ->searchable()
                    ->sortable()
                    ->default('-'), // 如果 admin_id 为 null
                Tables\Columns\TextColumn::make('type')
                    ->label('操作类型')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'increase' => '增加',
                        'decrease' => '减少',
                        'reset' => '重置',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'increase' => 'success',
                        'decrease' => 'warning',
                        'reset' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('变动/重置值')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_before')
                    ->label('操作前积分')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_after')
                    ->label('操作后积分')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('备注')
                    ->limit(30)
                    ->tooltip(fn (?string $state): ?string => $state) // 完整内容鼠标悬浮显示
                    ->toggleable(isToggledHiddenByDefault: true), // 默认隐藏，可切换显示
                Tables\Columns\TextColumn::make('created_at')
                    ->label('操作时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('用户'),
                Tables\Filters\SelectFilter::make('admin_id')
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->preload()
                    ->label('操作管理员'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'increase' => '增加',
                        'decrease' => '减少',
                        'reset' => '重置',
                    ])
                    ->label('操作类型'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('操作日期从'),
                        Forms\Components\DatePicker::make('created_until')->label('操作日期至'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // 通常不建议编辑日志记录，如果需要，取消注释
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // 通常不建议批量删除日志记录
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('created_at', 'desc'); // 默认按创建时间降序排列
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
            'index' => Pages\ListPointLogs::route('/'),
            // 如果不希望手动创建记录，可以注释掉下面这行
            // 'create' => Pages\CreatePointLog::route('/create'),
            // 'edit' => Pages\EditPointLog::route('/{record}/edit'), // 配合 EditAction
            // 'view' => Pages\ViewPointLog::route('/{record}'), // 配合 ViewAction
        ];
    }

    /**
     * 由于积分记录通常不应被用户直接创建或随意编辑核心数据（如积分变动前后值），
     * 我们可能希望禁用创建功能，并使编辑表单大部分只读。
     * 可以通过重写 canCreate() 方法来禁用创建按钮。
     */
    public static function canCreate(): bool
    {
        return false; // 设置为 false 来禁用创建按钮
    }
}