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
                            ->options([
                                'pending' => '待支付',
                                'approved' => '已报名',
                                'rejected' => '未通过',
                                'cancelled' => '已取消',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->label('支付金额')
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('支付方式')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_no')
                            ->label('支付单号')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('payment_time')
                            ->label('支付时间'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->tooltip(function ($record) {
                        return $record->phone . "\n" . $record->city;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('联系电话')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activity.title')
                    ->label('所属活动')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => '待支付',
                            'approved' => '已报名',
                            'rejected' => '未通过',
                            'cancelled' => '已取消'
                        };
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('支付金额')
                    ->tooltip(function ($record) {
                        return "方式: {$record->payment_method}\n单号: {$record->payment_no}\n时间: " . ($record->payment_time?->format('Y-m-d H:i') ?? '-');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_time')
                    ->label('支付时间')
                    ->dateTime()
                    ->sortable()
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('activity_id')
                    ->relationship('activity', 'title')
                    ->label('对应活动')
            ])
            ->actions([
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
