<?php

namespace App\Filament\Admin\Resources\CouponResource\Pages;

use App\Filament\Admin\Resources\CouponResource;
use App\Services\Stripe\Discount\CreateStripeCouponService;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            $couponService = new CreateStripeCouponService();

            $data['coupon_code'] = $couponService->createCouponCode($data);

            return $data;

        } catch (\Exception $e) {

            throw new \Exception('Falha ao criar o cupom: ' . $e->getMessage());
        }

        return $data;
    }

}
