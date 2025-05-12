<?php

namespace App\Filament\Admin\Resources\OrganizationResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionRefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscription_refunds';

    protected static ?string $modelLabel = 'Reembolsos';

    protected static ?string $modelLabelPlural = "Reembolsos";

    protected static ?string $title = 'Reembolsos da Subscription';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('object')
            ->columns([

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Valor')
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state / 100, 2, ',', '.')),

                TextColumn::make('reason')
                    ->label('Motivo')
                    ->alignCenter()
                    ->badge()
                    ->searchable(),

                TextColumn::make('failure_reason')
                    ->label('Motivo')
                    ->visible(fn ($record) => $record && $record->failure_reason !== null)
                    ->searchable(),

                TextColumn::make('reference')
                    ->label('Referência')
                    ->alignCenter()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:m:s')
                    ->alignCenter()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
