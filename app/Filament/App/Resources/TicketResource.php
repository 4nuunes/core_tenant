<?php

namespace App\Filament\App\Resources;

use App\Enums\TenantSuport\{TicketPriorityEnum, TicketTypeEnum};
use App\Filament\App\Resources\TicketResource\RelationManagers\TicketResponsesRelationManager;
use App\Filament\App\Resources\TicketResource\{Pages};
use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Forms\Components\{Fieldset, FileUpload, RichEditor, Select, TextInput};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\{Tables};
use Illuminate\Database\Eloquent\{Model};

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'fas-bullhorn';

    protected static ?string $navigationGroup = 'Suporte';

    protected static ?string $navigationLabel = 'Solicitações';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $modelLabelPlural = "Tickets";

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Classificação')
                    ->schema([
                        TextInput::make('title')
                            ->label('Assunto')
                            ->required()
                            ->maxLength(50),

                        Select::make('type')
                            ->label('Tipo')
                            ->options(TicketTypeEnum::class)
                            ->searchable()
                            ->required(),

                        Select::make('priority')
                            ->label('Prioridade')
                            ->options(TicketPriorityEnum::class)
                            ->searchable()
                            ->required(),
                    ])->columns(3),

                Fieldset::make('Detalhes do Ticket')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Detalhamento')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Fieldset::make('Anexos')
                    ->schema([
                        FileUpload::make('file')
                            ->multiple()
                            ->label('Arquivos'),

                        FileUpload::make('image_path')
                            ->label('Imagens')
                            ->image()
                            ->imageEditor(),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Solicitação')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Solicitante')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Assunto')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Prioridade')
                    ->alignCenter()
                    ->badge()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->alignCenter()
                    ->badge()
                    ->sortable(),

                TextColumn::make('lifetime')
                    ->label('Tempo de Vida')
                    ->getStateUsing(function (Model $record) {
                        $createdAt = Carbon::parse($record->created_at);
                        $closedAt  = $record->closed_at ? Carbon::parse($record->closed_at) : now();
                        $diff      = $createdAt->diff($closedAt);

                        return "{$diff->d} dias, {$diff->h} horas";

                    })
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:m:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:m:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([])
            ->groups([
                Group::make('user.name')
                    ->label('Usuário'),
                Group::make('status')
                    ->label('Status'),
                Group::make('type')
                    ->label('Tipo'),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TicketResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view'   => Pages\ViewTicket::route('/{record}'),
            'edit'   => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
