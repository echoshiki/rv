<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuGroupResource\Pages;
use App\Filament\Resources\MenuGroupResource\RelationManagers;
use App\Models\MenuGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuGroupResource extends Resource
{
    protected static ?string $model = MenuGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = '系统设置';

    protected static ?string $navigationLabel = '菜单管理';

    protected static ?string $label = '菜单组';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('名称')
                            ->required(),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('标识')
                            ->required()
                            ->unique(ignoreRecord: true),                
                        
                        Forms\Components\Select::make('layout')
                            ->label('布局类型')
                            ->columnSpanFull()
                            ->options([
                                'grid' => '网格布局',
                                'vertical' => '垂直布局',
                                'horizontal' => '水平布局'
                            ])
                            ->default('grid')
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('dedescription')
                            ->label('描述')
                            ->nullable()
                            ->placeholder('选填'),

                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->integer()
                            ->default(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label('启用')
                            ->columnSpanFull()
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('名称')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('slug')
                    ->label('标识'),
                
                Tables\Columns\TextColumn::make('layout')
                    ->label('布局类型')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'grid' => '网格布局',
                            'vertical' => '垂直布局',
                            'horizontal' => '水平布局'
                        };
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('状态'),
                
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime('Y-m-d'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('启用状态')
                    ->query(fn (Builder $query) => $query->where('is_active', true))
                    ->toggle(),
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
            // 关联器
            RelationManagers\MenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuGroups::route('/'),
            'create' => Pages\CreateMenuGroup::route('/create'),
            'edit' => Pages\EditMenuGroup::route('/{record}/edit'),
        ];
    }
}
