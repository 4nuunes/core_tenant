<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany};

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
        'stripe_id',
    ];

    protected $casts = [
        'image' => 'array',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function product_features(): HasMany
    {
        return $this->hasMany(ProductFeature::class);
    }
    public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }

}
