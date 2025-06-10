<?php

namespace App\Filament\Resources\RvOrderResource\Pages;

use App\Filament\Resources\RvOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRvOrders extends ListRecords
{
    protected static string $resource = RvOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
