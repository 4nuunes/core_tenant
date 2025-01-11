<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use function App\Support\tenant;

use App\Data\Cashier\Stripe;
use App\Models\Organization;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBillableIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();
        if ($user && $user->is_admin) {
            return $next($request);
        }
        
        $tenant = tenant(Organization::class);
 
        
        //dd($tenant);
        $stripeConfig = Stripe::fromConfig();

        foreach ($stripeConfig->plans() as $plan) {
            if ($tenant->subscribedToProduct($plan->productId())) {
                return $next($request);
            }
        }

        if ($request->getQueryString() === 'action=subscribe') {
            return $next($request);
        }
        

        return redirect(Dashboard::getUrl(['action' => 'subscribe']));
    }
}
