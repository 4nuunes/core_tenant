<?php

namespace App\Services\Stripe\Customer;

use App\Services\Traits\StripeClientTrait;
use Exception;
use Stripe\Customer;

class CreateStripeCustomerService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    /**
     * @param array
     * @return Customer
     * @throws Exception
     */
    public function createCustomer(array $data): Customer
    {
        try {

            return $this->stripe->customers->create([
                'name'        => $data['name'],
                'email'       => $data['email'],
                'phone'       => $data['phone'],
                'description' => 'Cliente registrado no sistema SaaS',
            ]);
        } catch (Exception $e) {

            throw new Exception('Falha ao criar cliente no Stripe: ' . $e->getMessage());
        }
    }
}
