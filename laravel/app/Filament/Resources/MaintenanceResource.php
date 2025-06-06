<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Filament\Resources\MaintenanceResource\RelationManagers;
use App\Models\Maintenance;
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

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = '互动管理';

    protected static ?string $navigationLabel = '维保预约';

    protected static ?string $modelLabel = '维保预约';

    protected static ?string $pluralModelLabel = '预约列表';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make() // 分组
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('用户')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->columnSpanFull()
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
                        Forms\Components\Textarea::make('issues')
                            ->label('维保事项')
                            ->rows(5)
                            ->columnSpanFull()
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
                Tables\Columns\TextColumn::make('province')
                    ->label('省份')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('城市')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d')
                    ->sortable()
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenances::route('/'),
            // 'create' => Pages\CreateMaintenance::route('/create'),
            'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // 设置为 false 来禁用创建按钮
    }
}
