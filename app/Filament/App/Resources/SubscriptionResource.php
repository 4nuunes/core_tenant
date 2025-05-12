<?php

namespace App\Filament\App\Resources;

use App\Enums\Stripe\{CancelSubscriptionEnum, SubscriptionStatusEnum};
use App\Filament\App\Resources\SubscriptionResource\{Pages};
use App\Models\{Subscription};
use App\Services\Stripe\Subscription\CancelSubscriptionService;
use Carbon\Carbon;
use Filament\Forms\Components\{Fieldset, Select, Textarea};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{Action, ActionGroup};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Forms\Components\RatingStar;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'fas-hand-holding-dollar';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Minhas Assinaturas';

    protected static ?string $modelLabel = 'Minha Assinatura';

    protected static ?string $modelLabelPlural = "Minhas Assinaturas";

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stripe_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => SubscriptionStatusEnum::from($state)->getLabel())
                    ->color(fn ($state) => SubscriptionStatusEnum::from($state)->getColor())
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('stripe_period')
                    ->label('Tipo do Plano')
                    ->getStateUsing(function ($record) {
                        // Acessa o preço relacionado via o relacionamento definido
                        return $record->price->interval;
                    })
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('stripe_price')
                    ->label('Valor do Plano')
                    ->getStateUsing(function ($record) {
                        // Acessa o preço relacionado via o relacionamento definido
                        return $record->price->unit_amount;
                    })
                    ->money('brl')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('trial_ends_at')
                    ->label('Fim Período de Teste')
                    ->alignCenter()
                    ->dateTime('d/m/Y'),

                TextColumn::make('current_period_start')
                    ->label('Inicio da Cobrança')
                    ->alignCenter()
                    ->dateTime('d/m/Y'),

                TextColumn::make('ends_at')
                    ->label('Expira em')
                    ->alignCenter()
                    ->dateTime('d/m/Y'),

                TextColumn::make('remaining_time')
                    ->label('Tempo Restante')

                    ->getStateUsing(function ($record) {
                        $endsAt = $record->ends_at ? Carbon::parse($record->ends_at) : null;

                        if (!$endsAt) {
                            return 'Sem data definida';
                        }

                        $now = now();

                        // Verifica se o plano já expirou
                        if ($now > $endsAt) {
                            return 'Expirado';
                        }

                        // Calcula a diferença total em dias e horas
                        $remainingDays  = $now->diffInDays($endsAt, false);
                        $remainingHours = $now->diffInHours($endsAt) % 24;

                        return sprintf('%d dias e %02d horas', $remainingDays, $remainingHours);
                    })
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Cancelar Assinatura')
                        ->form([

                            Fieldset::make('Motivo do Cancelamento')
                                ->schema([
                                    Select::make('reason')
                                        ->label('Selecione o Motivo')
                                        ->options(CancelSubscriptionEnum::class)
                                        ->required(),
                                ])->columns(1),

                            Fieldset::make('Suas Impressões')
                                ->schema([
                                    Textarea::make('coments')
                                        ->label('Comentário ou Feedback')
                                        ->rows(4)
                                        ->columnSpan('full'),
                                ])->columns(1),

                            Fieldset::make('Sua Nota')
                                ->schema([
                                    RatingStar::make('rating')
                                        ->label('Avaliação')
                                        ->required()
                                        ->columnSpan('full'),
                                ])->columns(1),

                        ])
                        ->requiresConfirmation()
                        ->modalHeading('Confirmar Cancelamento')
                        ->modalDescription(function ($record) {
                            // Usando Carbon para formatar a data ends_at
                            $endsAt = Carbon::parse($record->ends_at)->format('d/m/Y H:i'); // Formato desejado

                            return "Atenção!!! após o cancelamento você terá acesso a plataforma até: {$endsAt}, após essa data nenhuma cobrança será feita, seus acessos serão revogados e todos os dados serão apagados. Deseja continuar?";
                        })
                        ->slideOver()
                        ->slideOver()
                        ->action(function (Action $action, $record, array $data) {
                            try {

                                $cancellationService = new CancelSubscriptionService();
                                $cancellationService->cancel($record, $data);

                            } catch (\Exception $e) {

                                Notification::make()
                                    ->title('Erro ao Criar Preço')
                                    ->body('Ocorreu um erro ao criar o preço no Stripe: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })

                        ->color('danger')
                        ->icon('heroicon-o-key'),

                    Action::make('Baixar Invoice')
                        ->label('Baixar Invoice')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn ($record) => $record->invoice_pdf)
                        ->tooltip('Baixar PDF da Fatura')
                        ->color('primary'),
                ])
                ->icon('fas-sliders')
                ->color('warning'),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            //'create' => Pages\CreateSubscription::route('/create'),
            //'view' => Pages\ViewSubscription::route('/{record}'),
            //'edit'   => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
}
