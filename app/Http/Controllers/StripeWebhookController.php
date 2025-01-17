<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use App\Models\SubscriptionItem;
use App\Models\SubscriptionRefund;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Defina a chave secreta do webhook da Stripe
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        // Obtenha o conteúdo do payload e o cabeçalho de assinatura
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // Verificar a assinatura do webhook
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            // Armazenar o evento recebido no banco de dados
            WebhookEvent::create([
                'event_type' => $event->type,
                'payload'    => json_encode($event->data->object),
                'status'     => 'success',
            ]);

            // Processar o evento de acordo com seu tipo
            switch ($event->type) {
                case 'payment_method.attached':
                    $this->handlePaymentMethodAttached($event->data->object);
                    break;
                case 'customer.subscription.created':
                    $this->handleCustomerSubscriptionCreated($event->data->object);
                    break;
                case 'customer.subscription.updated':
                    $this->handleCustomerSubscriptionUpdated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    $this->handleCustomerSubscriptionDeleted($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    $this->handleCustomerPaymentSucceeded($event->data->object);
                    break;
                case 'charge.refund.updated':
                    $this->handleSubscriptionRefundUpdated($event->data->object);
                    break;

                    // Adicione outros eventos conforme necessário
                default:
                    break;
            }

            return response()->json(['status' => 'success'], 200);
        } catch (SignatureVerificationException $e) {
            // Armazenar falha de verificação de assinatura no banco de dados
            WebhookEvent::create([
                'event_type' => 'signature_verification_failed',
                'payload'    => json_encode(['error' => $e->getMessage()]),
                'status'     => 'failed',
            ]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }



    private function handlePaymentMethodAttached($paymentMethod)
    {
        // Verificar se o payment method está relacionado a um cliente
        $organization = Organization::where('stripe_id', $paymentMethod->customer)->first();

        if ($organization) {

            $organization->pm_type    = $paymentMethod->card->brand;
            $organization->pm_last_four    = $paymentMethod->card->last4;
            $organization->card_exp_month = $paymentMethod->card->exp_month;
            $organization->card_exp_year  = $paymentMethod->card->exp_year;
            $organization->card_country   = $paymentMethod->card->country;
            $organization->save();
        }
    }

    private function handleCustomerSubscriptionCreated($subscriptionMethod)
    {
        // Obter o customer_id da assinatura
        $customerId = $subscriptionMethod->customer;

        // Buscar a organização relacionada ao customer_id
        $organization = Organization::where('stripe_id', $customerId)->first();

        if ($organization) {
            // Criar uma nova assinatura associada à organização
            $newSubscription = new Subscription();
            $newSubscription->stripe_id = $subscriptionMethod->id; // ID da assinatura da Stripe
            $newSubscription->organization_id = $organization->id; // Associar a organização à assinatura

            // Definir os outros dados da assinatura
            $newSubscription->stripe_status = $subscriptionMethod->status;
            $newSubscription->type = $subscriptionMethod->plan->object;
            $newSubscription->quantity = $subscriptionMethod->quantity;
            $newSubscription->stripe_price = $subscriptionMethod->plan->id;

            $newSubscription->current_period_start = now()->setTimestamp($subscriptionMethod->current_period_start);
            $newSubscription->ends_at = now()->setTimestamp($subscriptionMethod->current_period_end);

            // Calcular a data de término do período de trial, se houver
            $trialPeriodDays = $subscriptionMethod->plan->trial_period_days ?? 0;
            $trialEndsAt = $trialPeriodDays > 0
                ? now()->setTimestamp($subscriptionMethod->current_period_start)->addDays($trialPeriodDays)
                : null;

            $newSubscription->trial_ends_at = $trialEndsAt;
            $newSubscription->ends_at = now()->setTimestamp($subscriptionMethod->current_period_end);

            // Salvar a nova assinatura
            $newSubscription->save();

            // Agora, vamos processar os itens da assinatura e inseri-los na tabela subscription_items
            if (isset($subscriptionMethod->items->data) && count($subscriptionMethod->items->data) > 0) {
                foreach ($subscriptionMethod->items->data as $item) {
                    // Criar um novo item de assinatura para cada item da assinatura
                    $newSubscriptionItem = new SubscriptionItem();
                    $newSubscriptionItem->subscription_id = $newSubscription->id; // Associar o item à nova assinatura
                    $newSubscriptionItem->stripe_id = $item->id;
                    $newSubscriptionItem->stripe_product = $item->price->product; // ID do produto relacionado
                    $newSubscriptionItem->stripe_price = $item->price->id; // ID do preço relacionado
                    $newSubscriptionItem->quantity = $item->quantity ?? 1; // Quantidade do item, se disponível

                    // Salvar o item de assinatura
                    $newSubscriptionItem->save();
                }
            }
        }
    }

    private function handleCustomerSubscriptionUpdated($subscriptionMethod)
    {
        // Encontrar a subscription pelo ID da assinatura Stripe
        $subscription = Subscription::where('stripe_id', $subscriptionMethod->id);

        if ($subscription) {

            // Calcular a data de término do período de trial, se existir
            $currentPeriodStart = $subscriptionMethod->current_period_start;
            $trialPeriodDays = $subscriptionMethod->plan->trial_period_days ?? 0;

            // Se houver período de trial, calcular a data de término
            $trialEndsAt = $trialPeriodDays > 0
                ? now()->setTimestamp($currentPeriodStart)->addDays($trialPeriodDays)
                : null;

            // Atualizar os campos da subscription
            $subscription->stripe_status = $subscriptionMethod->status;
            $subscription->trial_ends_at = $trialEndsAt;
            $subscription->ends_at = now()->setTimestamp($subscriptionMethod->current_period_end); // Final do período atual
            $subscription->current_period_start = now()->setTimestamp($subscriptionMethod->current_period_start); // Início do período atual


            // Usando update() passando um array com os dados a serem atualizados
            $subscription->update([
                'stripe_status' => $subscription->stripe_status,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
                'current_period_start' => $subscription->current_period_start,

            ]);
        }
    }

    private function handleCustomerSubscriptionDeleted($subscriptionMethod)
    {
        // Encontrar a subscription pelo ID da assinatura Stripe
        $subscription = Subscription::where('stripe_id', $subscriptionMethod->id);

        if ($subscription) {

            // Atualizar os campos da subscription
            $subscription->stripe_status = $subscriptionMethod->status;
            $subscription->trial_ends_at = null;
            $subscription->ends_at = now()->setTimestamp($subscriptionMethod->current_period_end); // Final do período atual
            $subscription->current_period_start = null;


            // Usando update() passando um array com os dados a serem atualizados
            $subscription->update([
                'stripe_status' => $subscription->stripe_status,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
                'current_period_start' => $subscription->current_period_start,


            ]);
        }
    }

    private function handleCustomerPaymentSucceeded($paymentMethod)
    {
        // Encontrar a subscription pelo ID da assinatura Stripe
        $subscription = Subscription::where('stripe_id', $paymentMethod->subscription);

        if ($subscription) {

            $subscription->update([
                'hosted_invoice_url' => $paymentMethod->hosted_invoice_url,
                'invoice_pdf' => $paymentMethod->invoice_pdf,
                'charge' => $paymentMethod->charge,
                'payment_intent' => $paymentMethod->payment_intent,
                'reference' => $paymentMethod->destination_details->card->reference,
                'reference_status' => $paymentMethod->destination_details->card->reference_status,
            ]);
        }
    }

    private function handleSubscriptionRefundUpdated($refundMethod)
    {
        // Encontrar a subscription pelo ID da assinatura Stripe
        $refund = SubscriptionRefund::where('refund_id', $refundMethod->id);

        if ($refund) {

            $refund->update([
                'status' => $refundMethod->status,
                'object' => $refundMethod->object,
                'balance_transaction' => $refundMethod->balance_transaction,
                'object' => $refundMethod->object,
                'reference' => $refundMethod->destination_details->card->reference,
                'reference_status' => $refundMethod->destination_details->card->reference_status,

            ]);
        }
    }
}
