<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Stripe\{ProductIntervalEnum, SubscriptionStatusEnum};
use App\Filament\Admin\Resources\OrganizationResource\Pages;
use App\Filament\Admin\Resources\OrganizationResource\RelationManagers\{SubscriptionRefundsRelationManager, SubscriptionRelationManager, UserRelationManager, WhatsappInstanceRelationManager};
use App\Models\{Organization, Price};
use Filament\Forms\Components\{Fieldset, Grid, TextInput};
use Filament\Forms\{Form, Set};
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\{ActionGroup, DeleteAction, EditAction, ViewAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Leandrocfe\FilamentPtbrFormFields\{Document, PhoneNumber};

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Tenant';

    protected static ?string $modelLabel = 'Tenant';

    protected static ?string $modelLabelPlural = "Tenants";

    protected static ?int $navigationSort = 1;

    public function getHeaderWidgetsColumns(): int
    {
        return 3;  // Definindo 3 colunas para os widgets
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Dados da Empresa')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome da Empresa')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Str::slug($state));
                            })
                            ->validationMessages([
                                'unique' => 'Empresa ja cadastrada.',
                            ])
                            ->maxLength(255),

                        Document::make('document_number')
                            ->label('Documento da Empresa (CPF ou CNPJ)')
                            ->validation(false)
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->dynamic()
                            ->validationMessages([
                                'unique' => 'Documento já cadastrado.',
                            ]),

                        TextInput::make('slug')
                            ->label('URL da Empresa')
                            ->readonly(),

                        TextInput::make('stripe_id')
                            ->label('Id Cliente Stripe')
                            ->readOnly()
                            ->maxLength(255),

                    ])->columns(3),

                Fieldset::make('Dados de Contato')
                    ->schema([
                        TextInput::make('email')
                            ->label('E-mail Empresa')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->validationMessages([
                                'unique' => 'E-mail já cadastrado.',
                            ]),

                        PhoneNumber::make('phone')
                            ->label('Telefone da Empresa')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->mask('(99) 99999-9999')
                            ->validationMessages([
                                'unique' => 'Telefone já cadastrado.',
                            ]),

                    ])->columns(2),

                Fieldset::make('Informações do Cartão')
                    ->schema([
                        Grid::make(5)->schema([

                            TextInput::make('pm_type')
                                ->label('Tipo de Pagamento')
                                ->readonly(),

                            TextInput::make('pm_last_four')
                                ->label('Últimos 4 Dígitos')
                                ->readonly(),

                            TextInput::make('card_exp_month')
                                ->label('Mês de Expiração')
                                ->readonly(),

                            TextInput::make('card_exp_year')
                                ->label('Ano de Expiração')
                                ->readonly(),

                            TextInput::make('card_country')
                                ->label('País do Cartão')
                                ->readonly(),
                        ]),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('latest_subscription_stripe_status')
                    ->label('Status Subscription')
                    ->getStateUsing(fn ($record) => $record->subscriptions()->latest('stripe_status')->first()?->stripe_status)
                    ->getStateUsing(function ($record) {
                        $status = $record->subscriptions()->latest('stripe_status')->first()?->stripe_status;

                        return $status ?? 'Admin Tenant';
                    })
                    ->formatStateUsing(function ($state) {
                        return $state === 'Admin Tenant' ? $state : SubscriptionStatusEnum::from($state)->getLabel();
                    })
                    ->color(function ($state) {
                        return $state === 'Admin Tenant' ? 'info' : SubscriptionStatusEnum::from($state)->getColor();
                    })
                    ->badge(),

                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Url Tenant')
                    ->searchable(),

                TextColumn::make('planperiod')
                    ->label('Plano Contratado')
                    ->getStateUsing(function ($record) {
                        $subscription = $record->subscriptions()->latest()->first();

                        if ($subscription) {
                            $stripePrice = $subscription->stripe_price;
                            $price       = Price::where('stripe_price_id', $stripePrice)->latest()->first();

                            return  $price->interval ?? 'N/A';
                        }

                        return 'N/A';
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === 'N/A') {
                            return $state;
                        }

                        if ($state instanceof ProductIntervalEnum) {
                            return $state->getLabel();
                        }

                        return ProductIntervalEnum::tryFrom($state)?->getLabel() ?? 'Desconhecido';
                    })
                    ->color(function ($state) {
                        if ($state === 'N/A') {
                            return 'info';
                        }

                        if ($state instanceof ProductIntervalEnum) {
                            return $state->getColor();
                        }

                        return ProductIntervalEnum::tryFrom($state)?->getColor() ?? 'secondary';
                    })
                    ->badge()
                    ->alignCenter(),

                TextColumn::make('planvalue')
                    ->label('Valor do Plano')
                    ->getStateUsing(function ($record) {
                        $subscription = $record->subscriptions()->latest()->first();

                        if ($subscription) {
                            $stripePrice = $subscription->stripe_price;
                            $price       = Price::where('stripe_price_id', $stripePrice)->latest()->first();

                            return $price ? $price->unit_amount : 'Preço não encontrado';
                        }

                        return 'N/A';
                    })
                    ->money('brl')
                    ->alignCenter(),

                TextColumn::make('latest_subscription_trial_ends_at')
                    ->label('Período de Teste')
                    ->getStateUsing(function ($record) {
                        $trialEndsAt = $record->subscriptions()->latest('trial_ends_at')->first()?->trial_ends_at;

                        if (is_null($trialEndsAt)) {
                            return 'N/A';
                        }

                        return now()->greaterThan($trialEndsAt) ? 'Período expirado' : $trialEndsAt;
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === 'N/A' || $state === 'Período expirado') {
                            return $state;
                        }

                        return \Carbon\Carbon::parse($state)->format('d/m/Y');
                    })
                    ->alignCenter(),

                TextColumn::make('latest_subscription_ends_at')
                    ->label('Expira Em')
                    ->getStateUsing(function ($record) {
                        $endsAt = $record->subscriptions()->latest('ends_at')->first()?->ends_at;

                        if ($endsAt) {
                            $remainingDays = now()->diffInDays($endsAt, false);

                            if ($remainingDays < 0) {
                                return 'Expirado';
                            }

                            return sprintf('%d dias', $remainingDays);
                        }

                        return 'Não Expira';
                    }),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable(),

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
                    ViewAction::make()
                        ->color('primary'),
                    EditAction::make()
                        ->color('secondary'),
                    DeleteAction::make(),
                ])
                ->icon('fas-sliders')
                ->color('warning'),
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
            UserRelationManager::class,
            SubscriptionRelationManager::class,
            SubscriptionRefundsRelationManager::class,
            WhatsappInstanceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view'   => Pages\ViewOrganization::route('/{record}'),
            'edit'   => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {

        return false;
    }
}
