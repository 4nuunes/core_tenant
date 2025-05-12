<?php

namespace App\Services\Stripe\Discount;

use App\Services\Traits\StripeClientTrait;

use Exception;

class CreateStripeCouponService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    /**
     * @param array
     * @return Discount
     * @throws Exception
     */
    public function createCouponCode(array $data): string
    {
        try {

            if (!isset($data['duration']) || !in_array($data['duration'], ['once', 'forever', 'repeating'])) {
                throw new Exception('A duração do cupom é inválida ou não foi especificada.');
            }

            $params = [
                'name'               => $data['name'] ?? null, // Nome do cupom (opcional)
                'percent_off'        => $data['percent_off'] ?? null, // Desconto percentual
                'currency'           => $data['currency'] ?? 'usd', // Moeda para desconto fixo
                'duration'           => $data['duration'], // Duração do desconto
                'duration_in_months' => $data['duration_in_months'] ?? null, // Necessário para duração "repeating"
                'max_redemptions'    => $data['max_redemptions'] ?? null, // Máximo de usos do cupom
                'redeem_by'          => isset($data['redeem_by']) ? strtotime($data['redeem_by']) : null, // Data limite de resgate
            ];

            // Remove parâmetros nulos
            $params = array_filter($params, fn ($value) => !is_null($value));

            // Cria o cupom no Stripe
            $coupon = $this->stripe->coupons->create($params);

            return $coupon->id;

        } catch (Exception $e) {

            throw new Exception('Falha ao criar código promocional no Stripe: ' . $e->getMessage());
        }

        return [];
    }
}
