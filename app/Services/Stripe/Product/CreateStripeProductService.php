<?php

namespace App\Services\Stripe\Product;

use App\Services\Traits\StripeClientTrait;
use Illuminate\Support\Facades\Log;

class CreateStripeProductService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    public function execute(string $name, ?string $description = null): string
    {
        try {
            $stripeProduct = $this->stripe->products->create([
                'name'        => $name,
                'description' => $description,
            ]);

            return $stripeProduct->id;
        } catch (\Exception $e) {
            //Log::error('Erro ao criar produto na Stripe: ' . $e->getMessage());
            throw new \Exception('Erro ao criar produto na Stripe.' . $e->getMessage());
        }
    }
}
