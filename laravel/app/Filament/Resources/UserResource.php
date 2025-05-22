<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = '用户管理';

    protected static ?string $label = '用户';

    protected static ?string $slug = 'users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereDoesntHave('roles');
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
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->label('手机号')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => '该手机号已存在',
                            ])
                            ->columnSpanFull()
                            ->required(),
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
                            ->hidden(fn (string $context): bool => $context === 'create')
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
                    ->label('会员等级')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
