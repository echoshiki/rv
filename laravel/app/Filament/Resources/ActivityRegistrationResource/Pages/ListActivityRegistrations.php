<?php

namespace App\Filament\Resources\ActivityRegistrationResource\Pages;

use App\Filament\Resources\ActivityRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityRegistrations extends ListRecords
{
    protected static string $resource = ActivityRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
