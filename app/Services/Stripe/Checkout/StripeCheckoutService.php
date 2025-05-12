<?php

declare(strict_types = 1);

namespace App\Services\Stripe\Checkout;

use App\Data\Stripe\StripeDataLoader;
use App\Models\Organization;
use App\Services\Traits\StripeClientTrait;

use function App\Support\tenant;

use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;

class StripeCheckoutService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    /**
     * @param string $billingPeriod
     * @return string
     * @throws \Exception
     */
    public function createCheckoutSession(string $billingPeriod): string
    {

        $organization = tenant(Organization::class);

        if (!$organization->stripe_id) {
            throw new \Exception('A organização não possui um ID do Stripe associado.');
        }

        // Obtém o produto e o preço com base no período de cobrança selecionado
        $products = StripeDataLoader::getProductsData();
        $priceId  = null;

        foreach ($products as $product) {
            foreach ($product['prices'] as $price) {
                if ($price['interval'] === $billingPeriod) {
                    $priceId = $price['stripe_price_id'];

                    break 2;
                }
            }
        }

        if (!$priceId) {
            throw new \Exception("Nenhum preço encontrado para o período de cobrança: {$billingPeriod}");
        }

        // Cria a sessão de checkout
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'subscription',
            'customer'             => $organization->stripe_id,
            'line_items'           => [
                [
                    'price'    => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => URL::to('/app'),
            'cancel_url'  => URL::to('/app'),
        ]);

        return $checkoutSession->url;
    }
}
