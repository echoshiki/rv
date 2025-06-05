<?php

namespace App\Filament\Resources\PointLogResource\Pages;

use App\Filament\Resources\PointLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointLog extends EditRecord
{
    protected static string $resource = PointLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
