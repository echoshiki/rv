<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityRegistrationResource\Pages;
use App\Filament\Resources\ActivityRegistrationResource\RelationManagers;
use App\Models\ActivityRegistration;
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
use App\Enums\RegistrationStatus;

class ActivityRegistrationResource extends Resource
{
    protected static ?string $model = ActivityRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = '活动管理';

    protected static ?string $navigationLabel = '报名列表';

    protected static ?string $label = '报名列表';

    protected static ?string $slug = 'activity-registrations';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('activity_id')
                            ->relationship('activity', 'title')
                            ->label('报名活动')
                            ->native(false)
                            ->disabled()
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('registration_no')
                            ->label('报名编号')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('用户')
                            ->native(false)
                            ->disabled()
                            ->required(),
                    ]),
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('联系电话')
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
                        Forms\Components\Textarea::make('remarks')
                            ->label('备注')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options(function () {
                                return RegistrationStatus::getLabels();
                            })
                            ->default(RegistrationStatus::Pending->value)
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('fee')
                            ->label('支付金额')
                            ->numeric()
                            ->default(0.00)
                    ]),
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('admin_remarks')
                            ->label('管理员备注')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('form_data')
                            ->label('原始数据')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_no')
                    ->label('报名编号')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('用户电话')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activity.title')
                    ->label('所属活动')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->formatStateUsing(fn (RegistrationStatus $state): string => $state->label())
                    ->color(fn (RegistrationStatus $state): string => $state->color())
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee')
                    ->label('支付金额')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('报名状态')
                    ->options(RegistrationStatus::getLabels())
                    ->multiple(), // 允许用户同时筛选多个状态

                Tables\Filters\SelectFilter::make('activity_id')
                    ->relationship('activity', 'title')
                    ->label('对应活动'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityRegistrations::route('/'),
            'create' => Pages\CreateActivityRegistration::route('/create'),
            'edit' => Pages\EditActivityRegistration::route('/{record}/edit'),
        ];
    }
}
