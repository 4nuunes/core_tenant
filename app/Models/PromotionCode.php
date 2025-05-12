<?php

namespace App\Models;

use App\Enums\Stripe\PromotionDurationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_promotional_code',
        'active',
        'code',
        'id_cupom_code',
        'duration',
        'duration_in_months',
        'percent_off',
        'max_redemptions',
        'redeem_by',
        'customer',
        'valid',
        'first_time_transaction',
        'expires_at',
    ];

    protected $casts = [
        'active'                 => 'boolean',
        'valid'                  => 'boolean',
        'first_time_transaction' => 'boolean',
        'duration'               => PromotionDurationEnum::class,
    ];
}
