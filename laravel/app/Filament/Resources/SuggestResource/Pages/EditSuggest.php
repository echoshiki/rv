<?php

namespace App\Filament\Resources\SuggestResource\Pages;

use App\Filament\Resources\SuggestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuggest extends EditRecord
{
    protected static string $resource = SuggestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
