<?php

namespace App\Services\Stripe\Subscription;

use App\Models\SubscriptionCancellation;
use App\Services\Traits\StripeClientTrait;
use Illuminate\Support\Facades\Notification;

class CancelSubscriptionService
{
    use StripeClientTrait;

    public function __construct()
    {
        $this->initializeStripeClient();
    }

    /**
     * @param mixed
     * @param array
     * @return void
     */
    public function cancel($record, array $data): void
    {
        try {

            $this->stripe->subscriptions->update($record->stripe_id, [
                'cancel_at_period_end' => true,
            ]);

            SubscriptionCancellation::create([
                'organization_id' => $record->organization_id,
                'stripe_id'       => $record->stripe_id,
                'reason'          => $data['reason'],
                'coments'         => $data['coments'],
                'rating'          => $data['rating'],
            ]);

            Notification::make()
                ->title('Assinatura Cancelada')
                ->body('Assinatura cancelada com sucesso!')
                ->success()
                ->send();

        } catch (\Exception $e) {

            Notification::make()
                ->title('Erro ao Cancelar')
                ->body('Ocorreu um erro ao cancelar a assinatura. Tente novamente mais tarde.')
                ->danger()
                ->send();

        }
    }
}
