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
                Forms\Components\Select::make('activity_id')
                    ->relationship('activity', 'title')
                    ->label('对应活动')
                    ->native(false)
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('发布用户')
                    ->native(false)
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('报名人')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('联系电话')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('province')
                    ->label('省')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('市')
                    ->maxLength(255),
                Forms\Components\TextInput::make('registration_no')
                    ->label('报名编号')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->label('状态')
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
                Forms\Components\TextInput::make('form_data')
                    ->label('表单数据'),
                Forms\Components\Textarea::make('admin_remarks')
                    ->label('管理员备注')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('remarks')
                    ->label('备注')
                    ->columnSpanFull(),
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
                        return match($state) {
                            'pending' => '待支付',
                            'approved' => '已报名',
                            'rejected' => '未通过',
                            'cancelled' => '已取消'
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
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
