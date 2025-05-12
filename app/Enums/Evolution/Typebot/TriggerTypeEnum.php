<?php

namespace App\Enums\Evolution\Typebot;

use Filament\Support\Contracts\{HasLabel};

enum TriggerTypeEnum: string implements HasLabel
{
    case ALL      = 'all';
    case NONE     = 'none';
    case ADVANCED = 'advanced';
    case KEYWORD  = 'keyword';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL      => 'Todos',
            self::NONE     => 'Nenhum',
            self::ADVANCED => 'Começa com',
            self::KEYWORD  => 'Palavra Chave',

        };
    }

}
