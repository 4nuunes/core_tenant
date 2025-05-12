<?php

namespace App\Models;

use App\Enums\Stripe\{ProductCurrencyEnum, PromotionDurationEnum};
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'coupon_code',
        'duration',
        'duration_in_months',
        'percent_off',
        'max_redemptions',
        'is_active',
        'redeem_by',
        'currency',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'redeem_by' => 'datetime',
        'duration'  => PromotionDurationEnum::class,
        'currency'  => ProductCurrencyEnum::class,
    ];
}
