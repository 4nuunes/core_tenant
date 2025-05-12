<?php

namespace App\Filament\App\Resources\WhatsappInstanceResource\RelationManagers;

use App\Enums\Evolution\Typebot\{TriggerOperatorEnum, TriggerTypeEnum};
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\{Section, Select, TextInput};
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\{TextColumn, ToggleColumn};
use Filament\Tables\Table;
use Filament\{Tables};

class InstanceTypebotsRelationManager extends RelationManager
{
    protected static string $relationship = 'InstanceTypebots';

    protected static ?string $modelLabel = 'Robô TypeBot';

    protected static ?string $modelLabelPlural = "TypeBots";

    protected static ?string $title = 'Robôs TypeBot';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados Basicos do typebot')
                    ->schema([

                        TextInput::make('name')
                            ->label('Descrição do Robô')
                            ->prefixIcon('fas-id-card')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('url')
                            ->label('URL do typebot')
                            ->prefix('https://')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('type_bot')
                            ->prefixIcon('fas-robot')
                            ->label('Typebot')
                            ->required()
                            ->maxLength(255),

                    ])->columns(3),

                Section::make('Dados de Disparo')
                    ->schema([

                        Select::make('trigger_type')
                            ->label('Tipo de Gatilho')
                            ->required()
                            ->reactive()
                            ->live()
                            ->options(TriggerTypeEnum::class),

                        Select::make('trigger_operator')
                            ->hidden(fn ($get) => $get('trigger_type') != 'keyword')
                            ->required()
                            ->reactive()
                            ->label('Operador de Gatilho')
                            ->options(TriggerOperatorEnum::class),

                        TextInput::make('trigger_value')
                            ->hidden(fn ($get) => !in_array($get('trigger_type'), ['advanced', 'keyword']))
                            ->label('Valor do Gatilho')
                            ->prefixIcon('fas-keyboard')
                            ->reactive()
                            ->required()
                            ->maxLength(255),

                    ])->columns(3),

                Section::make('Configurações Gerais')
                ->schema([

                    TextInput::make('expire')
                        ->label('Expirar em minutos')
                        ->prefixIcon('fas-clock')
                        ->numeric()
                        ->required(),

                    TextInput::make('keyword_finish')
                        ->label('Palavra-chave de Finalização')
                        ->prefixIcon('fas-arrow-right-from-bracket')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('delay_message')
                        ->label('Atraso Padrão (Milisegundos)')
                        ->prefixIcon('fas-clock')
                        ->required()
                        ->numeric(),

                    TextInput::make('unknown_message')
                        ->label('Mensagem Desconhecida')
                        ->prefixIcon('fas-question')
                        ->required()
                        ->maxLength(30),

                    TextInput::make('debounce_time')
                        ->label('Tempo de Debounce')
                        ->prefixIcon('fas-clock')
                        ->required()
                        ->numeric(),

                ])->columns(3),

                Section::make('Opções Gerais')
                ->schema([

                    ToggleButtons::make('listening_from_me')
                        ->label('Ouvindo de mim')
                        ->inline()
                        ->boolean()
                        ->required(),

                    ToggleButtons::make('stop_bot_from_me')
                        ->label('Parar bot por mim')
                        ->inline()
                        ->boolean()
                        ->required(),

                    ToggleButtons::make('keep_open')
                        ->label('Manter aberto')
                        ->inline()
                        ->boolean()
                        ->required(),

                ])->columns(3),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Descrição do'),

                TextColumn::make('url')
                    ->label('URL'),

                TextColumn::make('type_bot')
                    ->label('Codigo do Typebot'),

                TextColumn::make('id_typebot')
                    ->label('Id do Bot'),

                ToggleColumn::make('is_active')
                    ->label('Ativo')
                    ->alignCenter(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
