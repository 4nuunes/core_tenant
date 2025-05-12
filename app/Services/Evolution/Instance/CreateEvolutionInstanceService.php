<?php

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;

class CreateEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function createInstance(array $data)
    {
        // Formatando o número (removendo tudo que não for número)
        $formattedNumber = preg_replace('/\D/', '', $data['number']);

        // Construção do Payload
        $payload = [
            'instanceName'    => $data['name'],
            'number'          => $formattedNumber,
            'qrcode'          => true,
            'integration'     => "WHATSAPP-BAILEYS",
            'rejectCall'      => (bool) $data['reject_call'],
            'msgCall'         => $data['msg_call'] ?? '',
            'groupsIgnore'    => (bool) $data['groups_ignore'],
            'alwaysOnline'    => (bool) $data['always_online'],
            'readMessages'    => (bool) $data['read_messages'],
            'readStatus'      => (bool) $data['read_status'],
            'syncFullHistory' => (bool) $data['sync_full_history'],
            'webhook'         => [
                'url'      => config('services.evolution.url_webhook'),
                'byEvents' => false,
                'base64'   => false,
                'events'   => [
                    'APPLICATION_STARTUP',
                    'QRCODE_UPDATED',
                    'CONNECTION_UPDATE',
                    'NEW_TOKEN',
                    'SEND_MESSAGE',
                    'PRESENCE_UPDATE',
                    'TYPEBOT_START',
                    'TYPEBOT_CHANGE_STATUS',
                    //'MESSAGES_SET',
                    //'MESSAGES_UPSERT',
                    //'MESSAGES_UPDATE',
                    //'MESSAGES_DELETE',
                    //'CONTACTS_SET',
                    //'CONTACTS_UPSERT',
                    //'CONTACTS_UPDATE',
                    //'CHATS_SET',
                    //'CHATS_UPSERT',
                    //'CHATS_UPDATE',
                    //'CHATS_DELETE',
                    //'GROUPS_UPSERT',
                    //'GROUP_UPDATE',
                    //'GROUP_PARTICIPANTS_UPDATE',
                    //'LABELS_EDIT',
                    //'LABELS_ASSOCIATION',
                    //'CALL',
                ],
            ],

        ];

        // Realizando a Requisição
        $response = $this->makeRequest('/instance/create', 'POST', $payload);

        // Extrai os dados da Resposta
        $instanceId = $response['instance']['instanceId'];
        $status     = $response['instance']['status'];
        $hash       = $response['hash'];
        $base64     = $response['qrcode']['base64'];

        // Retornando os Dados para resource
        return [
            'instance_id' => $instanceId,
            'status'      => $status,
            'hash'        => $hash,
            'qr_code'     => $base64,
        ];
    }

}
