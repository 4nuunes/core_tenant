<?php

namespace App\Filament\App\Resources\TicketResource\RelationManagers;

use Filament\Forms\Components\{Fieldset, FileUpload, Textarea};
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\{Tables};

class TicketResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketresponses';

    protected static ?string $modelLabel = 'Tratativa';

    protected static ?string $modelLabelPlural = "Tratativas";

    protected static ?string $title = 'Tratativa do Ticket';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Tratativa do Ticket')
                ->schema([
                    Textarea::make('message')
                        ->label('Resposta')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(1),

                Fieldset::make('Anexos')
                ->schema([
                    FileUpload::make('file')
                        ->multiple()
                        ->label('Arquivos'),
                ])->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('user.name')
                    ->label('Responsável'),

                TextColumn::make('message')
                    ->label('Tratativa'),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:m:s'),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:m:s'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([

            ]);
    }
}
