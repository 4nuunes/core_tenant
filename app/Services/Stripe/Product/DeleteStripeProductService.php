<?php

namespace App\Services\Stripe\Product;

use App\Models\Price;
use App\Services\Traits\StripeClientTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class DeleteStripeProductService
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
    public function execute(object $productRecord): void
    {
        try {

            $hasPrices = Price::where('product_id', $productRecord->id)->exists();

            if ($hasPrices) {
                throw new Exception('Não é possível excluir o produto. Existem preços cadastrados relacionados a ele.');
            }

            $this->stripe->products->delete($productRecord->stripe_id);

            $productRecord->delete();
        } catch (Exception $e) {

            //Log::error('Erro ao excluir produto na Stripe: ' . $e->getMessage());

            throw new Exception('Erro ao excluir o produto: ' . $e->getMessage());
        }
    }
}
