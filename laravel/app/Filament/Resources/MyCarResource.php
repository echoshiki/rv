<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyCarResource\Pages;
// use App\Filament\Resources\MyCarResource\RelationManagers; // 如果有相关模型管理，取消注释
use App\Models\MyCar;
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

class MyCarResource extends Resource
{
    protected static ?string $model = MyCar::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '用户管理';

    protected static ?string $navigationLabel = '我的爱车';

    protected static ?string $modelLabel = '爱车';

    protected static ?string $pluralModelLabel = '爱车列表';

    protected static ?int $navigationSort = 3; // 调整在导航栏的排序

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('个人信息') // 分组
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('用户')
                            ->relationship('user', 'name') // 假设 User 模型有 name 字段
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('手机号')
                            ->tel()
                            ->required()
                            ->maxLength(255),
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
                        
                    ]),
                Forms\Components\Section::make('车型信息') // 分组
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('brand')
                            ->label('车型')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('vin')
                            ->label('车架号')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // 编辑时忽略当前记录
                        Forms\Components\TextInput::make('licence_plate')
                            ->label('车牌号')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // 编辑时忽略当前记录
                        Forms\Components\DatePicker::make('listing_at')
                            ->label('上牌日期'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('手机号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('车型')
                    ->searchable(),
                Tables\Columns\TextColumn::make('licence_plate')
                    ->label('车牌号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vin')
                    ->label('车架号')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // 默认隐藏，可切换显示
                Tables\Columns\TextColumn::make('listing_at')
                    ->label('上牌日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthday')
                    ->label('生日')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('province')
                    ->label('省份')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')
                    ->label('城市')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 如果需要，可以添加表格过滤器
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            // RelationManagers 如果有的话
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyCars::route('/'),
            'create' => Pages\CreateMyCar::route('/create'),
            'edit' => Pages\EditMyCar::route('/{record}/edit')
        ];
    }
}
