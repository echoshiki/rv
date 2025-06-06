<?php

namespace App\Filament\Resources\SuggestResource\Pages;

use App\Filament\Resources\SuggestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuggests extends ListRecords
{
    protected static string $resource = SuggestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
