<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'current_period_start',
        'trial_ends_at',
        'ends_at',
        'hosted_invoice_url',
        'invoice_pdf',
        'charge',
        'payment_intent',

    ];

    public function scopeActive($query)
    {
        return $query->where('ends_at', '>', now()) // Verifica se a assinatura ainda é válida
                     //->where('stripe_status', 'active') // Adiciona verificação do status da assinatura
                    ->latest('created_at'); // Ordena pela assinatura mais recente
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function price(): HasOne
    {
        return $this->hasOne(Price::class, 'stripe_price_id', 'stripe_price');
    }

    public function subscription_refunds(): HasMany
    {
        return $this->hasMany(SubscriptionRefund::class);
    }

}
