<?php

namespace App\Enums\Stripe\Refunds;

use Filament\Support\Contracts\HasLabel;

enum ReasonRefundStatusEnum: string implements HasLabel
{
    case CHARGE_FOR_PENDING_REFUND_DISPUTED = 'charge_for_pending_refund_disputed';
    case DECLINED                           = 'declined';
    case EXPIRED_OR_CANCELED_CARD           = 'expired_or_canceled_card';
    case INSUFFICIENT_FUNDS                 = 'insufficient_funds';
    case LOST_OR_STOLEN_CARD                = 'lost_or_stolen_card';
    case MERCANT_REQUEST                    = 'merchant_request';
    case UNKNOWN                            = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::CHARGE_FOR_PENDING_REFUND_DISPUTED => 'Constestação de reembolso',
            self::DECLINED                           => 'Reembolso recusado',
            self::EXPIRED_OR_CANCELED_CARD           => 'Expirada ou Cancelada',
            self::INSUFFICIENT_FUNDS                 => 'Saldo Insuficiente',
            self::LOST_OR_STOLEN_CARD                => 'Perda ou roubo do cartão',
            self::MERCANT_REQUEST                    => 'Falha no reembolso',
            self::UNKNOWN                            => ' falhou por motivo desconhecido',
        };
    }

}
