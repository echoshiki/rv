<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\PointLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\RegionService;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = '用户管理';

    protected static ?string $label = '注册用户';

    protected static ?string $slug = 'users';

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->whereDoesntHave('roles');
    // }

    // 2025.07.07 优化查询性能
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select(['id', 'name', 'phone', 'level', 'points', 'created_at'])
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->whereNull('model_has_roles.model_id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('用户名')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => '用户名已存在',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->label('手机号')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => '该手机号已存在',
                            ])
                            ->required(),
                        Forms\Components\Select::make('sex')
                            ->label('性别')
                            ->options([
                                1 => '男',
                                2 => '女',
                            ])
                            ->default(1)
                            ->native(false),
                        Forms\Components\DatePicker::make('birthday')
                            ->label('生日'),
                        Forms\Components\Select::make('province')
                            ->label('省')
                            ->options(function () {
                                $regionService = app(RegionService::class);
                                $provinces = $regionService->getProvinces();
                                return array_column($provinces, 'name', 'code');
                            })
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set) {
                                $set('city', null);
                            })
                            ->native(false),
                        Forms\Components\Select::make('city')
                            ->label('市')
                            ->options(function (Get $get) {
                                $regionService = app(RegionService::class);
                                $provinceCode = $get('province');
                                if (!$provinceCode) {
                                    return [];
                                }
                                $cities = $regionService->getCities($provinceCode);
                                return array_column($cities, 'name', 'code');
                            })
                            ->native(false),
                        Forms\Components\Textarea::make('address')
                            ->label('详细地址')
                            ->columnSpanFull()
                            ->maxLength(65535),
                        Forms\Components\Select::make('level')
                            ->label('会员等级')
                            ->options(User::getLevels())
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('points')
                            ->numeric()
                            ->label('会员积分')
                            ->columnSpanFull()
                            ->required(),
                        // 创建日期 创建时不显示
                        Forms\Components\DatePicker::make('created_at')
                            ->label('创建时间')
                            ->columnSpanFull()
                            // 创建页面时不显示
                            ->hidden(fn(string $context): bool => $context === 'create')
                            ->disabled()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    // 显示长度
                    ->limit(20)
                    ->label('用户名'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('手机号'),
                Tables\Columns\TextColumn::make('level_name')
                    ->badge()
                    ->label('会员等级'),
                Tables\Columns\TextColumn::make('points')
                    ->sortable()
                    ->label('会员积分'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label('创建时间')
                    ->date('Y-m-d'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->options(User::getLevels())
                    ->label('会员等级'),
                Tables\Filters\TernaryFilter::make('phone')
                    ->label('有无手机号')
                    ->placeholder('全部用户') // "全部"状态的提示文字
                    ->trueLabel('有手机号')   // "是"状态的标签
                    ->falseLabel('无手机号')  // "否"状态的标签
                    ->queries(
                        // 当选择"有手机号"时，执行此查询
                        true: fn (Builder $query) => $query->whereNotNull('phone')->where('phone', '<>', ''),
                        // 当选择"无手机号"时，执行此查询
                        false: fn (Builder $query) => $query->whereNull('phone')->orWhere('phone', '=', '')
                    )
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage_points')
                    ->label('调整积分')
                    ->icon('heroicon-o-currency-dollar')
                    ->modalHeading(fn (User $record) => "调整用户 {$record->name} 的积分")
                    ->modalSubmitActionLabel('确认调整')
                    ->form(fn (User $record) => [
                        Placeholder::make('current_points')
                            ->label('当前积分')
                            ->content($record->points),
                        Forms\Components\Select::make('operation_type')
                            ->label('操作类型')
                            ->options([
                                'increase' => '增加积分',
                                'decrease' => '减少积分',
                                'reset' => '重置积分',
                            ])
                            ->required()
                            ->live() // live() 用于动态显示/隐藏下面的字段
                            ->native(false),
                        Forms\Components\TextInput::make('points_value')
                            ->label(fn (Get $get): string => match ($get('operation_type')) {
                                'increase' => '增加数量',
                                'decrease' => '减少数量',
                                'reset' => '重置为',
                                default => '积分值'
                            })
                            ->numeric()
                            ->required()
                            ->minValue(0) // 对于增加/减少，可能需要大于0，重置可以为0
                            ->helperText(function (Get $get): ?string {
                                if ($get('operation_type') === 'decrease') {
                                    return '减少后的积分不能为负。';
                                }
                                return null;
                            }),
                        Forms\Components\Textarea::make('remarks')
                            ->label('备注')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, User $record) {
                        $user = $record;
                        $pointsBefore = $user->points;
                        $amount = (int) $data['points_value'];
                        $newPoints = $pointsBefore;

                        DB::transaction(function () use ($user, $data, $pointsBefore, &$newPoints, $amount) {
                            switch ($data['operation_type']) {
                                case 'increase':
                                    $newPoints = $pointsBefore + $amount;
                                    break;
                                case 'decrease':
                                    $newPoints = $pointsBefore - $amount;
                                    if ($newPoints < 0) {
                                        Notification::make()
                                            ->title('操作失败')
                                            ->body('减少后的积分不能为负数。')
                                            ->danger()
                                            ->send();
                                        return; // 或者可以抛出 ValidationException
                                    }
                                    break;
                                case 'reset':
                                    $newPoints = $amount; // 重置为指定值
                                    break;
                            }

                            $user->points = $newPoints;
                            $user->save();

                            PointLog::create([
                                'user_id' => $user->id,
                                'admin_id' => Auth::id(), // 当前登录的管理员ID
                                'type' => $data['operation_type'],
                                'amount' => $data['operation_type'] === 'reset' ? $newPoints : $amount, // 对于重置，记录的是重置后的值，其他是变动值
                                'points_before' => $pointsBefore,
                                'points_after' => $newPoints,
                                'remarks' => $data['remarks'],
                            ]);

                            Notification::make()
                                ->title('积分调整成功')
                                ->success()
                                ->send();
                        });
                    }),
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
