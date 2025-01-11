<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions;
use Stripe\StripeClient;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\OrganizationResource;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    
}

