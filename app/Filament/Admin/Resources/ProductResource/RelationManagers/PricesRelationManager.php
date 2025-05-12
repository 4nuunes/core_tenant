<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use App\Enums\Stripe\{ProductCurrencyEnum, ProductIntervalEnum};
use App\Services\Stripe\Price\CreateStripePriceService;
use Filament\Forms\Components\{Select, TextInput};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\{TextColumn, ToggleColumn};
use Filament\Tables\Table;
use Filament\{Tables};
use Leandrocfe\FilamentPtbrFormFields\Money;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $modelLabel = 'Preço';

    protected static ?string $modelLabelPlural = "Preço";

    protected static ?string $title = 'Valores dos Produtos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('currency')
                    ->label('Moeda')
                    ->required()
                    ->searchable()
                    ->options(ProductCurrencyEnum::class),

                Select::make('interval')
                    ->label('Intervalo de Cobrança')
                    ->options(ProductIntervalEnum::class)
                    ->searchable()
                    ->required(),

                Money::make('unit_amount')
                    ->label('Preço')
                    ->default('100,00')
                    ->required(),

                TextInput::make('trial_period_days')
                    ->label('Periodo de testes')
                    ->required()
                    ->default(0)
                    ->integer(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price')
            ->columns([
                TextColumn::make('stripe_price_id')
                    ->label('Id Gateway Pagamento')
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Moeda')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('interval')
                    ->label('Intervalo de Cobrança')
                    ->badge()
                    ->sortable()
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Ativo para cliente')
                    ->alignCenter(),

                TextColumn::make('unit_amount')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('trial_period_days')
                    ->label('Dias de Teste')
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record) {
                        try {

                            $createStripePriceService = new CreateStripePriceService();
                            $createStripePriceService->execute($record);

                        } catch (\Exception $e) {

                            Notification::make()
                                ->title('Erro ao Criar Preço')
                                ->body('Ocorreu um erro ao criar o preço no Stripe: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
