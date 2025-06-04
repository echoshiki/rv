<?php

namespace App\Filament\Resources\RvCategoryResource\Pages;

use App\Filament\Resources\RvCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRvCategories extends ListRecords
{
    protected static string $resource = RvCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
