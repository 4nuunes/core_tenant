<?php

namespace App\Models;

use App\Enums\Stripe\{ProductCurrencyEnum, ProductIntervalEnum};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stripe_price_id',
        'currency',
        'is_active',
        'trial_period_days',
        'interval',
        'unit_amount',
    ];

    protected $casts = [
        'interval' => ProductIntervalEnum::class,
        'currency' => ProductCurrencyEnum::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
