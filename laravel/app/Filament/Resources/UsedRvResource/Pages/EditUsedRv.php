<?php

namespace App\Filament\Resources\UsedRvResource\Pages;

use App\Filament\Resources\UsedRvResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsedRv extends EditRecord
{
    protected static string $resource = UsedRvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
