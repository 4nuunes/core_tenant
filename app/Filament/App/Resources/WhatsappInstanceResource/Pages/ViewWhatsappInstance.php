<?php

namespace App\Filament\App\Resources\WhatsappInstanceResource\Pages;

use App\Filament\App\Resources\WhatsappInstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWhatsappInstance extends ViewRecord
{
    protected static string $resource = WhatsappInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
