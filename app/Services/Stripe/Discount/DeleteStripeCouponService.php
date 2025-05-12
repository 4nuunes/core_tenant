<?php

namespace App\Services\Stripe\Discount;

use App\Services\Traits\StripeClientTrait;

use Exception;

class DeleteStripeCouponService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    /**
     * @param string $couponId
     * @return bool
     * @throws Exception
     */
    public function deleteCouponCode(string $couponId): bool
    {
        try {

            // Cria o cupom no Stripe
            $this->stripe->coupons->delete($couponId);

            return true;

        } catch (Exception $e) {

            throw new Exception('Falha ao deletar o cupom no Stripe: ' . $e->getMessage());
        }

        return [];
    }
}
