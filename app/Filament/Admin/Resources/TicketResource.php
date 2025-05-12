<?php

namespace App\Filament\Admin\Resources;

use App\Enums\TenantSuport\{TicketPriorityEnum, TicketStatusEnum, TicketTypeEnum};
use App\Filament\Admin\Resources\TicketResource\RelationManagers\TicketResponsesRelationManager;
use App\Filament\Admin\Resources\TicketResource\{Pages};
use App\Models\{Organization, Ticket};
use Carbon\Carbon;
use Filament\Forms\Components\{Fieldset, FileUpload, RichEditor, Select, TextInput};
use Filament\Forms\{Form, Set};
use Filament\Resources\Resource;
use Filament\Tables\Actions\{ActionGroup, DeleteAction, EditAction, ViewAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\{Tables};
use Illuminate\Database\Eloquent\{Model};

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'fas-comment-dots';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Solicitações';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $modelLabelPlural = "Tickets";

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNotIn('status', [
            TicketStatusEnum::CLOSED->value,
            TicketStatusEnum::RESOLVED->value,
        ])->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Fieldset::make('Empresa')
                    ->schema([
                        TextInput::make('title')
                            ->label('Assunto')
                            ->required()
                            ->maxLength(50),

                        Select::make('organization_id')
                            ->label('Empresa')
                            ->required()
                            ->options(Organization::all()->pluck('name', 'id')) // Exibe todas as organizações
                            ->afterStateUpdated(function (Set $set, $state) {
                                // Limpar o campo de usuário quando a organização for alterada
                                $set('user_id', null);
                            }),

                        Select::make('user_id')
                            ->label('Usuario')
                            ->searchable()   // Permite pesquisa
                            ->preload()      // Carrega os dados de forma antecipada
                            ->live()          // Atualiza as opções em tempo real
                            ->required()
                            ->options(function ($get) {
                                // Obter o ID da organização selecionada
                                $organizationId = $get('organization_id');

                                // Verificar se a organização foi selecionada
                                if ($organizationId) {
                                    // Carregar os membros (usuários) da organização selecionada
                                    $organization = Organization::find($organizationId);

                                    if ($organization) {
                                        // Acessar os membros e retornar um array de opções
                                        return $organization->members->pluck('name', 'id')->toArray(); // Usando pluck para extrair os dados
                                    }
                                }

                                // Se não houver organização selecionada, retornar um array vazio
                                return [];
                            }),
                    ])->columns(3),

                Fieldset::make('Classificação')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(TicketStatusEnum::class)
                            ->searchable()
                            ->required(),

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

                TextColumn::make('organization.name')
                    ->label('Tenant')
                    ->numeric()
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('closed_at')
                    ->label('Fechado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
