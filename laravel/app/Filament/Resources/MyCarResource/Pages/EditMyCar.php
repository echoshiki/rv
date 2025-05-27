<?php

namespace App\Filament\Resources\MyCarResource\Pages;

use App\Filament\Resources\MyCarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyCar extends EditRecord
{
    protected static string $resource = MyCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
