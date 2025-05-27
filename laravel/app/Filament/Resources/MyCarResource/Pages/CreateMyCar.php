<?php

namespace App\Filament\Resources\MyCarResource\Pages;

use App\Filament\Resources\MyCarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyCar extends CreateRecord
{
    protected static string $resource = MyCarResource::class;
}
