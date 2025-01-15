<?php

namespace App\Filament\Admin\Resources\WebhookEventResource\Pages;

use App\Filament\Admin\Resources\WebhookEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWebhookEvent extends ViewRecord
{
    protected static string $resource = WebhookEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
