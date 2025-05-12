<?php

namespace App\Enums\Stripe;

use Filament\Support\Contracts\{HasLabel};

enum PromotionDurationEnum: string implements HasLabel
{
    case FOREVER   = 'forever';
    case ONCE      = 'once';
    case REPEATING = 'repeating';

    public function getLabel(): string
    {
        return match ($this) {
            self::FOREVER   => 'Sempre Ativo',
            self::ONCE      => 'Uma Vez',
            self::REPEATING => 'Recorrente',
        };
    }

}
