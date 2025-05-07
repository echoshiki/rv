<?php

namespace App\Filament\Resources\MenuGroupResource\Pages;

use App\Filament\Resources\MenuGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuGroup extends EditRecord
{
    protected static string $resource = MenuGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
