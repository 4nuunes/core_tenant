<?php

namespace App\Filament\Admin\Resources\WebhookEventResource\Pages;

use App\Filament\Admin\Resources\WebhookEventResource;
use App\Filament\Admin\Resources\WebhookEventResource\Widgets\StatsWebhookOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebhookEvents extends ListRecords
{
    protected static string $resource = WebhookEventResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            StatsWebhookOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
