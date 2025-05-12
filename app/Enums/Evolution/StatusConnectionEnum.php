<?php

namespace App\Enums\Evolution;

use Filament\Support\Contracts\{HasColor, HasLabel};

enum StatusConnectionEnum: string implements HasLabel, HasColor
{
    case CLOSE      = 'close';
    case OPEN       = 'open';
    case CONNECTING = 'connecting';
    case REFUSED    = 'refused';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPEN       => 'Conectado',
            self::CONNECTING => 'Conectando',
            self::CLOSE      => 'Desconectado',
            self::REFUSED    => 'Recusado',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OPEN       => 'success',
            self::CONNECTING => 'warning',
            self::CLOSE      => 'danger',
            self::REFUSED    => 'danger',
        };
    }

}
