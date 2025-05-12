<?php

namespace App\Models;

use App\Enums\Stripe\Refunds\{ReasonRefundStatusEnum, RefundStatusEnum, RefundSubscriptionEnum};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class SubscriptionRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'organization_id',
        'refund_id',
        'status',
        'currency',
        'balance_transaction',
        'amount',
        'reason',
        'object',
        'reference',
        'reference_status',
        'failure_reason',
    ];

    protected $casts = [
        'reason'         => RefundSubscriptionEnum::class,
        'status'         => RefundStatusEnum::class,
        'failure_reason' => ReasonRefundStatusEnum::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // Relacionamento com Organization (um SubscriptionRefund pertence a uma Organization)
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);  // Ajuste conforme o nome correto do seu modelo
    }
}
