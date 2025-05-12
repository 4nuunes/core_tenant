<?php

namespace App\Filament\App\Resources\WhatsappInstanceResource\Pages;

use App\Filament\App\Resources\WhatsappInstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsappInstances extends ListRecords
{
    protected static string $resource = WhatsappInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
