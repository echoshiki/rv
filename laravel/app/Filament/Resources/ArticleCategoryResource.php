<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Filament\Resources\ArticleCategoryResource\RelationManagers;
use App\Models\Article;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = '内容管理';

    protected static ?string $navigationLabel = '文章分类';

    protected static ?string $label = '文章分类';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        // Forms\Components\Select::make('parent_id')
                        //     ->label('父级分类')
                        //     ->relationship('parent', 'title')
                        //     ->searchable()
                        //     ->preload()
                        //     ->native(false)
                        //     ->options(function (?ArticleCategory $record) {
                        //         $query = ArticleCategory::query();
                        //         if ($record) {
                        //             // 排除自身及其所有后代 (递归查询，可能影响性能，大型数据集需优化)
                        //             $excludeIds = collect([$record->id])->merge(
                        //                 $record->descendants()->pluck('id') // 需要实现 descendants 递归方法
                        //             )->all();
                        //             $query->whereNotIn('id', $excludeIds);
                        //         }
                        //         return $query->pluck('title', 'id');
                        //     })
                        //     ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label('分类名称')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('标识')
                            ->placeholder('选填')
                            ->unique(ArticleCategory::class, 'code', ignoreRecord: true)
                            ->columnSpanFull()
                            ->disabled(function (?ArticleCategory $record): bool {
                                // 无法修改受保护的分类
                                return $record && in_array($record->code, ArticleCategory::getProtectedCode() ?? []);
                            })       
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('描述')
                            ->placeholder('请输入描述，选填')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->width(100)
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('父级分类')
                    ->width(200)
                    ->placeholder('顶级分类')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('分类名称')
                    ->width(200)
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('标识')
                    ->width(200)
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('articles_count')
                    ->label('文章数量')
                    ->counts('articles'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_articles')
                    ->label('文章列表')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    // 确保 ArticleResource 有 category_id 过滤器
                    ->url(fn (ArticleCategory $record): string =>
                        ArticleResource::getUrl('index', ['tableFilters' => ['category_id' => ['value' => $record->id]]])
                    ),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('parent_id', 'asc');
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
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }

    // UI 层面保护核心分类不被删除
    public static function canDelete($record): bool
    {
        if ($record->is_single_page && $record->code && in_array($record->code, ArticleCategory::getProtectedCode())) {
            return false;
        }
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
