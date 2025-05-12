<?php

namespace App\Filament\Admin\Resources\OrganizationResource\Pages;

use App\Filament\Admin\Resources\OrganizationResource;
use App\Filament\Admin\Resources\OrganizationResource\Widgets\StatsTenantOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsTenantOverview::class,

        ];
    }
    public function getHeaderWidgetsColumns(): int
    {
        return 3;  // Definindo 3 colunas para os widgets
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
