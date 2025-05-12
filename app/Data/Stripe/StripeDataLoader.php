<?php

declare(strict_types = 1);

namespace App\Data\Stripe;

use App\Enums\Stripe\ProductIntervalEnum;
use App\Models\{Price, Product, ProductFeature};
use Illuminate\Support\Collection;

class StripeDataLoader
{
    /**
     * @return Collection
     */
    public static function getProductsData(): Collection
    {
        return Product::with(['features', 'prices'])
        ->where('is_active', true)
        ->get()

        ->map(function (Product $product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'image'       => $product->image,
                'stripe_id'   => $product->stripe_id,
                'features'    => $product->features->filter(function (ProductFeature $feature) {
                    return $feature->is_active == true;
                })->map(function (ProductFeature $feature) {
                    return [
                        'id'          => $feature->id,
                        'name'        => $feature->name,
                        'description' => $feature->description,
                        'is_active'   => $feature->is_active,
                    ];
                }),
                'prices' => $product->prices->filter(function (Price $price) {
                    return $price->is_active == true;
                })->map(function (Price $price) {
                    return [
                        'id'                   => $price->id,
                        'currency'             => $price->currency->value,
                        'interval'             => ProductIntervalEnum::from($price->interval->value)->getlabel(),
                        'interval_description' => ProductIntervalEnum::from($price->interval->value)->getDescription(),
                        'unit_amount'          => (int) $price->unit_amount,
                        'stripe_price_id'      => $price->stripe_price_id,
                        'is_active'            => $price->is_active,
                        'trial_period_days'    => (int) $price->trial_days,
                    ];
                }),
            ];
        });
    }
}
