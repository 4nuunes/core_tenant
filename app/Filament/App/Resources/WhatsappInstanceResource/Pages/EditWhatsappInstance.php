<?php

namespace App\Filament\App\Resources\WhatsappInstanceResource\Pages;

use App\Filament\App\Resources\WhatsappInstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsappInstance extends EditRecord
{
    protected static string $resource = WhatsappInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
