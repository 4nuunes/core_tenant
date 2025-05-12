<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organization;
use App\Services\Stripe\Customer\CreateStripeCustomerService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\{Form, Set};
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Leandrocfe\FilamentPtbrFormFields\{Document, PhoneNumber};

class RegisterOrganization extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Registrar Empresa';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label('Nome da Empresa')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('email')
                    ->label('E-mail Principal')
                    ->unique(Organization::class, 'email', ignoreRecord: true)
                    ->email()
                    ->required()
                    ->prefixIcon('fas-envelope')
                    ->validationMessages([
                        'unique' => 'E-mail já cadastrado.',
                    ]),

                PhoneNumber::make('phone')
                    ->label('Celular da Empresa')
                    ->unique(Organization::class, 'phone', ignoreRecord: true)
                    ->required()
                    ->mask('(99) 99999-9999')
                    ->prefixIcon('fas-phone')
                    ->validationMessages([
                        'unique' => 'Telefone ja cadastrado.',
                    ]),

                Document::make('document_number')
                    ->label('Documento da Empresa (CPF ou CNPJ)')
                    ->unique(Organization::class, 'document_number', ignoreRecord: true)
                    ->validation(false)
                    ->required()
                    ->dynamic()
                    ->prefixIcon('fas-id-card')
                    ->validationMessages([
                        'unique' => 'Documento já cadastrado.',
                    ]),

                TextInput::make('slug')
                    ->label('Essa será a URL da sua empresa')
                    ->unique(Organization::class, 'slug', ignoreRecord: true)
                    ->readonly()
                    ->prefixIcon('fas-globe')
                    ->validationMessages([
                        'unique' => 'Url em Uso, altere nome da empresa',
                    ]),
            ]);
    }

    protected function handleRegistration(array $data): Organization
    {
        $createStripeCustomerService = new CreateStripeCustomerService();

        $customer = $createStripeCustomerService->createCustomer($data);

        $organization = Organization::create(array_merge($data, [
            'stripe_id' => $customer->id,
        ]));

        // Vincula o usuário autenticado como membro da organização
        $organization->members()->attach(Auth::user());

        return $organization;
    }
}
