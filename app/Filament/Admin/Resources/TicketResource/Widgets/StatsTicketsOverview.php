<?php

namespace App\Filament\Admin\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsTicketsOverview extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            Stat::make('Atendimento em Progresso', Ticket::where('status', 'in_progress')->whereNull('closed_at')->count())
                ->description('Total')
                ->descriptionIcon('heroicon-s-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Bugs Abertos', Ticket::where('type', 'problem')->whereNull('closed_at')->count())
                ->description('Bugs')
                ->descriptionIcon('heroicon-s-bug-ant')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Melhorias Propostas', Ticket::where('type', 'enhancement')->count())
                ->description('Melhorias')
                ->color('success')
                ->descriptionIcon('heroicon-s-cog-6-tooth')
                ->chart([7, 3, 4, 5, 6, 3, 5, 5]),

            Stat::make('Tempo Médio de Resolução', function () {
                $tickets = Ticket::whereNotNull('closed_at')->get(['created_at', 'closed_at']);

                if ($tickets->isEmpty()) {
                    return '0,00 horas';
                }

                $totalHours = $tickets->reduce(function ($carry, $ticket) {
                    return $carry + $ticket->created_at->diffInHours($ticket->closed_at);
                }, 0);

                $averageTime = $totalHours / $tickets->count();

                return number_format(max($averageTime, 0), 2, ',', '.') . ' horas';
            })
                ->description('tempo')
                ->color('warning')
                ->descriptionIcon('heroicon-s-clock')
                ->chart([7, 3, 4, 5, 6, 3, 5, 5]),

        ];
    }
}
