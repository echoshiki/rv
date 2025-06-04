<?php

namespace App\Filament\Resources\RvCategoryResource\Pages;

use App\Filament\Resources\RvCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRvCategory extends EditRecord
{
    protected static string $resource = RvCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
