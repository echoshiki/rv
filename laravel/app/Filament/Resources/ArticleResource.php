<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontFamily;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = '内容管理';

    protected static ?string $navigationLabel = '文章管理';

    protected static ?string $label = '文章';

    protected static ?string $pluralLabel = '文章列表';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('主要内容')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('分类')
                            ->relationship('category', 'title')
                            ->columnSpanFull()
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('标题')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('video')
                            ->label('视频')
                            ->directory('articles/'. now()->format('Ymd'))
                            ->disk('public')
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('内容')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('articles/'. now()->format('Ymd'))
                            ->fileAttachmentsVisibility('public')
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
                        Forms\Components\TextInput::make('sort')
                            ->label('排序值')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\DatePicker::make('published_at')
                            ->label('发布时间')
                            ->required()
                            ->default(now()),
                        Forms\Components\Toggle::make('is_recommend')
                            ->label('是否推荐') 
                            ->default(false)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('category.title')
                    ->label('分类')
                    ->badge()
                    ->searchable()
                    ->placeholder('无分类'),
                Tables\Columns\ImageColumn::make('cover')
                    ->label('封面')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->limit(60)
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->fontFamily(FontFamily::Mono)
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('分类')
                    ->relationship('category', 'title')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('是否启用')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_recommend')
                    ->label('是否推荐')
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
