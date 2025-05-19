<?php

namespace App\Filament\Resources\MenuGroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'menuItems';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $label = '菜单项';

    protected static ?string $title = '菜单项';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->required(),
                
                Forms\Components\TextInput::make('subtitle')
                    ->label('副标题'),

                Forms\Components\Select::make('link_type')
                    ->label('链接类型')
                    ->options([
                        'page' => '页面',
                        'url' => '网址',
                        'function' => '函数调用',
                        'channel' => '分类传参',
                    ])
                    ->native(false)
                    ->required(),
                      
                Forms\Components\TextInput::make('link_value')
                    ->label('链接值')
                    ->required()
                    ->helperText('页面名称、网址或函数名'),
                
                Forms\Components\FileUpload::make('icon')
                    ->label('图标')
                    ->image()
                    ->directory('menu/icons'),
                
                Forms\Components\FileUpload::make('cover')
                    ->label('封面')
                    ->image()
                    ->directory('menu/covers'),
                
                Forms\Components\Toggle::make('requires_auth')
                    ->label('需要登录')
                    ->default(false),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('启用')
                    ->default(true),
                
                Forms\Components\TextInput::make('sort')
                    ->label('排序')
                    ->integer()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label('图标')
                    ->size(40),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('link_type')
                    ->label('链接类型'),
                
                Tables\Columns\TextColumn::make('link_value')
                    ->label('链接值')
                    ->limit(30),
                
                Tables\Columns\IconColumn::make('requires_auth')
                    ->label('需要登录'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('状态'),
                
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}