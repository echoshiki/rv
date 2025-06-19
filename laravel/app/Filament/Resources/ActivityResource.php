<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontFamily;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = '活动管理';

    protected static ?string $navigationLabel = '活动列表';

    protected static ?string $label = '活动';

    protected static ?string $pluralLabel = '活动列表';

    protected static ?string $slug = 'activities';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('活动信息')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('活动分类')
                            ->relationship('category', 'title')
                            ->columnSpanFull()
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('活动标题')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('content')
                            ->label('活动内容')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('activities/' . now()->format('Ymd'))
                            ->fileAttachmentsVisibility('private')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('报名设置')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('registration_start_at')
                        ->label('报名开始时间')
                        ->default(now())
                        ->nullable(),
                    Forms\Components\DatePicker::make('registration_end_at')
                        ->label('报名结束时间')
                        ->nullable(),
                    Forms\Components\TextInput::make('registration_fee')
                        ->label('报名费用')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('max_participants')
                        ->label('最大报名人数')
                        ->helperText('0表示不限制')
                        ->default(0)
                        ->numeric(),
                    Forms\Components\DatePicker::make('started_at')
                        ->label('活动开始时间'),
                    Forms\Components\DatePicker::make('ended_at')
                        ->label('活动结束时间'),
                ]),
                
                Forms\Components\Section::make('其他设置')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('cover')
                            ->label('封面')
                            ->image()
                            ->required()
                            ->directory('activities/'. now()->format('Ymd'))
                            ->disk('public')
                            ->imageEditor()
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('description')
                            ->label('活动摘要')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('code')
                            ->label('标识')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                        Forms\Components\DatePicker::make('published_at')
                            ->label('发布时间')
                            ->default(now())
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_recommend')
                            ->label('是否推荐') 
                            ->default(false)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('是否启用') 
                            ->default(true)
                            ->columnSpanFull()
                            ->required(),
                    ]),
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
                    ->label('活动分类')
                    ->badge()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover')
                    ->label('封面')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('活动标题')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_start_at')
                    ->label('报名开始时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('registration_end_at')
                    ->label('报名结束时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('活动开始时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ended_at')
                    ->label('活动结束时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('最大报名人数')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('current_participants')
                    ->label('报名人数')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->date('Y-m-d')
                    ->fontFamily(FontFamily::Mono)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'title')
                    ->label('活动分类'),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => '是',
                        false => '否',
                    ])
                    ->label('是否启用'),
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
