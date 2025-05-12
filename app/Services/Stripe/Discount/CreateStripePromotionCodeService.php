<?php

namespace App\Services\Stripe\Discount;

use App\Services\Traits\StripeClientTrait;

use Exception;

class CreateStripePromotionCodeService
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
    public function createPromotionCode(array $data): array
    {
        try {
            // Verifica se o cupom já existe, caso contrário, cria um novo

            return $data; // Retorna os dados atualizados

        } catch (Exception $e) {
            throw new Exception('Falha ao criar código promocional no Stripe: ' . $e->getMessage());
        }

        // Em caso de falha, retorna um array vazio ou qualquer valor padrão
        return []; // Garantir que sempre retorne um array
    }
}
