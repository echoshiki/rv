<?php

namespace App\Filament\Resources\RvOrderResource\Pages;

use App\Filament\Resources\RvOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRvOrder extends EditRecord
{
    protected static string $resource = RvOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
