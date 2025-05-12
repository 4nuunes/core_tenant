<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\TicketResource;
use App\Filament\Admin\Resources\TicketResource\Widgets\StatsTicketsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsTicketsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
