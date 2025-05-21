<?php

namespace App\Filament\Resources\UsedRvResource\Pages;

use App\Filament\Resources\UsedRvResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsedRvs extends ListRecords
{
    protected static string $resource = UsedRvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
