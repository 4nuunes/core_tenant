<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Mail\WelcomeUserMail;
use App\Models\Organization;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected $plainPassword;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $password            = $this->generateRandomPassword(10);
        $this->plainPassword = $password;

        $data['password']          = bcrypt($password);
        $data['email_verified_at'] = now();
        $data['is_tenant_admin']   = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

        // Busca a organização à qual o usuário está associado
        $organization = Organization::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();

        // Se a organização for encontrada, envia o e-mail
        if ($organization) {
            // Envia o e-mail com o nome da organização
            Mail::to($user->email)->queue(new WelcomeUserMail($user->name, $this->plainPassword, $organization->name));
        }
    }
    //Função para gerar Senha Aleatória
    protected function generateRandomPassword($length = 10)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_-+=<>?';

        return substr(str_shuffle($characters), 0, $length);
    }

    //retornar para a lista de usuários
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
