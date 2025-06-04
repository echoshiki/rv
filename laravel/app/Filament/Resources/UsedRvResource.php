<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsedRvResource\Pages;
use App\Filament\Resources\UsedRvResource\RelationManagers;
use App\Models\UsedRv;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsedRvResource extends Resource
{
    protected static ?string $model = UsedRv::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = '房车管理';
    protected static ?string $navigationLabel = '二手车管理';
    protected static ?string $label = '二手车列表';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('二手车信息')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('名称')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover')
                            ->label('封面')
                            ->image()
                            ->required()
                            ->directory('used_rvs/'. now()->format('Ymd'))
                            ->disk('public')
                            ->imageEditor()
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->label('价格')
                            ->numeric()
                            ->default(0),
                        Forms\Components\RichEditor::make('content')
                            ->label('详情')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('used_rvs/'. now()->format('Ymd'))
                            ->fileAttachmentsVisibility('private')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('photos')
                            ->label('相册')
                            ->multiple()
                            ->directory('used_rvs/'. now()->format('Ymd'))
                            ->disk('public')
                            ->imageEditor()
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('是否启用')
                            ->default(true)
                            ->required()
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('封面')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('价格'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('是否启用')
                    ->native(false),
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
            'index' => Pages\ListUsedRvs::route('/'),
            'create' => Pages\CreateUsedRv::route('/create'),
            'edit' => Pages\EditUsedRv::route('/{record}/edit'),
        ];
    }
}
