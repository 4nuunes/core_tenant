<?php

namespace App\Models;

use App\Enums\Stripe\CancelSubscriptionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'stripe_id',
        'reason',
        'coments',
        'rating',
    ];

    protected $casts = [
        'reason' => CancelSubscriptionEnum::class,
    ];
}
