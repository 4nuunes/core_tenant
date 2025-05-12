<?php

namespace App\Enums\Stripe;

use Filament\Support\Contracts\{HasColor, HasDescription, HasLabel};

enum CancelSubscriptionEnum: string implements HasLabel, HasColor, HasDescription
{
    case CUSTUMER_SERVICE = 'customer_service';
    case LOW_QUALITY      = 'low_quality';
    case MISSING_FEATURES = 'missing_features';
    case SWITCHED_SERVICE = 'switched_service';
    case TOO_COMPLEX      = 'too_complex';
    case TOO_EXPENSIVE    = 'too_expensive';
    case UNUSED           = 'unused';

    public function getLabel(): string
    {
        return match ($this) {
            self::CUSTUMER_SERVICE => 'Atendimento Ruim',
            self::LOW_QUALITY      => 'Baixa Qualidade',
            self::MISSING_FEATURES => 'Falta Recursos',
            self::SWITCHED_SERVICE => 'Mudando de Provedor',
            self::TOO_COMPLEX      => 'Muito Complexo',
            self::TOO_EXPENSIVE    => 'Muito caro',
            self::UNUSED           => 'Não Uso',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CUSTUMER_SERVICE => 'success',
            self::LOW_QUALITY      => 'success',
            self::MISSING_FEATURES => 'success',
            self::SWITCHED_SERVICE => 'success',
            self::TOO_COMPLEX      => 'success',
            self::TOO_EXPENSIVE    => 'success',
            self::UNUSED           => 'success',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::CUSTUMER_SERVICE => 'O atendimento ao cliente foi menor do que o esperado',
            self::LOW_QUALITY      => 'A qualidade ficou abaixo do esperado',
            self::MISSING_FEATURES => 'Alguns recursos estão faltando',
            self::SWITCHED_SERVICE => 'Estou mudando para um serviço diferente',
            self::TOO_COMPLEX      => 'A facilidade de uso foi menor do que o esperado',
            self::TOO_EXPENSIVE    => 'É muito caro',
            self::UNUSED           => 'Não uso o serviço o suficiente',

        };
    }
}
