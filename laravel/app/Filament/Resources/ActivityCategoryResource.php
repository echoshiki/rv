<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityCategoryResource\Pages;
use App\Filament\Resources\ActivityCategoryResource\RelationManagers;
use App\Models\ActivityCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ActivityCategoryResource extends Resource
{
    protected static ?string $model = ActivityCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = '活动管理';

    protected static ?string $navigationLabel = '活动分类';

    protected static ?string $label = '活动分类';

    protected static ?string $slug = 'activity-categories';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('分类名称')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('标识')
                            ->placeholder('选填')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->placeholder('请输入描述，选填')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('是否启用')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\TextColumn::make('activities_count')
                    ->label('活动数量')
                    ->counts('activities'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('是否启用')
                    ->width(100)
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_activities')
                    ->label('活动列表')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    // 确保 ActivityResource 有 category_id 过滤器
                    ->url(fn (ActivityCategory $record): string =>
                        ActivityResource::getUrl('index', ['tableFilters' => ['category_id' => ['value' => $record->id]]])
                    ),
                Tables\Actions\EditAction::make()
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
            'index' => Pages\ListActivityCategories::route('/'),
            'create' => Pages\CreateActivityCategory::route('/create'),
            'edit' => Pages\EditActivityCategory::route('/{record}/edit'),
        ];
    }
}
