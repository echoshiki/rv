<?php

namespace App\Filament\Resources\RvResource\Pages;

use App\Filament\Resources\RvResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRvs extends ListRecords
{
    protected static string $resource = RvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
