<?php

namespace App\Filament\App\Resources\TicketResource\Pages;

use Filament\Actions;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Resources\TicketResource;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;        
        return $data;      
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record; // Recupera o ticket recém-criado ou atualizado

        

        Notification::make()
            ->title('Chamado Registrado com Sucesso')
            ->body("Seu Chamado de N. {$ticket->id} foi registrado com sucesso. Em breve será respondido pela equipe.")
            ->success()
            ->actions([
                Action::make('Visualizar')
                    ->url(TicketResource::getUrl('view', ['record' => $ticket->id])),
                   
                ])
            ->sendToDatabase(Auth::user()); // Envia para o usuário relacionado ao ticket
            

            }

    }

