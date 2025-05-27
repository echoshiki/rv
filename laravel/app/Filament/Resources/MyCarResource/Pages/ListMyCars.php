<?php

namespace App\Filament\Resources\MyCarResource\Pages;

use App\Filament\Resources\MyCarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyCars extends ListRecords
{
    protected static string $resource = MyCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
