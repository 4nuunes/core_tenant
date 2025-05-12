<?php

namespace App\Services\Stripe\Price;

use App\Services\Traits\StripeClientTrait;
use Exception;

class CreateStripePriceService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    /**
     * @param object
     * @return void
     * @throws Exception
     */
    public function execute(object $record): void
    {
        try {

            $productId = $record->product->stripe_id;

            $unitAmount = (int)(str_replace(',', '', $record->unit_amount) * 100);

            $stripePrice = $this->stripe->prices->create([
                'currency'    => $record->currency->value,
                'unit_amount' => $unitAmount,
                'recurring'   => [
                    'interval'          => $record->interval->value,
                    'trial_period_days' => $record->trial_period_days,
                ],
                'product' => $productId,
            ]);

            $record->update(['stripe_price_id' => $stripePrice->id]);

        } catch (Exception $e) {

            throw new Exception('Erro ao criar preço no Stripe: ' . $e->getMessage());
        }
    }
}
