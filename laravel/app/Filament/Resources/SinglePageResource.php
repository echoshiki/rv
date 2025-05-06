<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SinglePageResource\Pages;
use App\Filament\Resources\SinglePageResource\RelationManagers;
use App\Models\SinglePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SinglePageResource extends Resource
{
    protected static ?string $model = SinglePage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = '内容管理';

    protected static ?string $navigationLabel = '单页管理';

    protected static ?string $label = '单页列表';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('主要内容')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->required()
                        ->columnSpanFull()
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('content')
                        ->label('内容')
                        ->required()
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('articles/'. now()->format('Ymd'))
                        ->fileAttachmentsVisibility('private')
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('设置')
                ->columns(2)
                ->schema([
                    Forms\Components\FileUpload::make('cover')
                        ->label('封面图片')
                        ->image()
                        ->required()
                        ->directory('articles/'. now()->format('Ymd'))
                        ->disk('public')
                        ->imageEditor()
                        ->nullable()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('description')
                        ->label('摘要')
                        ->nullable()
                        ->columnSpanFull()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('code')
                        ->label('业务代码')
                        ->required()
                        ->nullable()
                        ->placeholder('选填')
                        ->disabled(function (SinglePage $record): bool {
                            return $record && in_array($record->code, SinglePage::getProtectedCode() ?? []);
                        }),
                    Forms\Components\TextInput::make('sort')
                        ->label('排序值')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\DatePicker::make('published_at')
                        ->label('发布时间')
                        ->required()
                        ->default(now()),
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
            Tables\Columns\TextColumn::make('code')
                ->label('分类标识'),
            Tables\Columns\TextColumn::make('title')
                ->label('单页标题')
                ->limit(80)
                ->searchable(),
            Tables\Columns\TextColumn::make('published_at')
                ->label('发布时间')
                ->date('Y-m-d')
                ->sortable(),
            Tables\Columns\IconColumn::make('is_active')
                ->label('是否启用')
                ->boolean()
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\TernaryFilter::make('is_active')
                ->label('是否启用')
                ->native(false),
        ])
        ->actions([
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
            'index' => Pages\ListSinglePages::route('/'),
            'create' => Pages\CreateSinglePage::route('/create'),
            'edit' => Pages\EditSinglePage::route('/{record}/edit'),
        ];
    }
}
