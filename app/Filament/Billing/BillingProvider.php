<?php

declare(strict_types = 1);

namespace App\Filament\Billing;

use App\Http\Middleware\VerifyBillableIsSubscribed;
use App\Models\Organization;

use function App\Support\tenant;

use Closure;
use Filament\Billing\Providers\Contracts\Provider;
use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Laravel\Cashier\{Billable, Cashier};
use LogicException;

class BillingProvider implements Provider
{
    public function getRouteAction(): string|Closure|array
    {
        return static function (): RedirectResponse {
            $tenant = tenant(Organization::class);

            if ($tenant::class !== Cashier::$customerModel) {
                throw new LogicException('Filament tenant does not match the Cashier customer model');
            }

            if (!in_array(Billable::class, class_uses_recursive($tenant), true)) {
                throw new LogicException('Tenant model does not use Cashier Billable trait');
            }

            if (!$tenant->hasStripeId()) {
                $tenant->createAsStripeCustomer();
            }

            return $tenant->redirectToBillingPortal(Dashboard::getUrl());
        };
    }

    public function getSubscribedMiddleware(): string
    {
        return VerifyBillableIsSubscribed::class;
    }
}
