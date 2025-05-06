<?php

namespace App\Filament\Resources\SinglePageResource\Pages;

use App\Filament\Resources\SinglePageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSinglePages extends ListRecords
{
    protected static string $resource = SinglePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
