<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RvCategoryResource\Pages;
use App\Filament\Resources\RvCategoryResource\RelationManagers;
use App\Models\RvCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RvCategoryResource extends Resource
{
    protected static ?string $model = RvCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = '房车管理';

    protected static ?string $navigationLabel = '底盘管理';

    protected static ?string $label = '底盘管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('底盘名称')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('标识')
                    ->placeholder('选填')
                    ->unique(RvCategory::class, 'code', ignoreRecord: true)
                    ->columnSpanFull()      
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->label('描述')
                    ->placeholder('请输入描述，选填')
                    ->columnSpanFull()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('底盘名称')
                    ->width(200)
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('标识')
                    ->width(200)
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rvs_count')
                    ->label('车型数量')
                    ->counts('rvs'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_rvs')
                    ->label('车型列表')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    // 确保 RvResource 有 category_id 过滤器
                    ->url(fn (RvCategory $record): string =>
                        RvResource::getUrl('index', ['tableFilters' => ['category_id' => ['value' => $record->id]]])
                    ),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListRvCategories::route('/'),
            'create' => Pages\CreateRvCategory::route('/create'),
            'edit' => Pages\EditRvCategory::route('/{record}/edit'),
        ];
    }
}
