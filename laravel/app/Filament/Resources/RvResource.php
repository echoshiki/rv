<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RvResource\Pages;
use App\Filament\Resources\RvResource\RelationManagers;
use App\Models\Rv;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class RvResource extends Resource
{
    protected static ?string $model = Rv::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = '房车管理';

    protected static ?string $navigationLabel = '新车管理';

    protected static ?string $label = '新车';

    protected static ?string $pluralLabel = '新车列表';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('房车信息')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('底盘')
                            ->relationship('category', 'title')
                            ->columnSpanFull()
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('名称')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover')
                            ->label('封面')
                            ->image()
                            ->required()
                            ->directory('rvs/'. now()->format('Ymd'))
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->label('价格')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('order_price')
                            ->label('定金')
                            ->numeric()
                            ->default(0),
                        Forms\Components\RichEditor::make('content')
                            ->label('详情')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('rvs/'. now()->format('Ymd'))
                            ->fileAttachmentsVisibility('private')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('photos')
                            ->label('相册')
                            ->multiple()
                            ->directory('rvs/'. now()->format('Ymd'))
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover')
                    ->label('封面')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('底盘')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('价格'),
                Tables\Columns\TextColumn::make('order_price')
                    ->label('定金'),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('底盘')
                    ->relationship('category', 'title')
                    ->native(false),
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
            'index' => Pages\ListRvs::route('/'),
            'create' => Pages\CreateRv::route('/create'),
            'edit' => Pages\EditRv::route('/{record}/edit'),
        ];
    }
}
