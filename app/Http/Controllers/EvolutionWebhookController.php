<?php

namespace App\Http\Controllers;

use App\Enums\Evolution\StatusConnectionEnum;
use App\Models\{Organization, WebhookEvent, WhatsappInstance};
use App\Services\Evolution\Instance\FetchEvolutionInstanceService;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EvolutionWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Obtenha o conteúdo do payload e o cabeçalho de assinatura
        $payload = $request->getContent();

        // Armazenar o evento recebido no banco de dados
        WebhookEvent::create([
            'event_type' => $request->input('event'),
            'payload'    => json_encode($request->all()),
            'status'     => 'success',
        ]);

        // Processar o evento de acordo com seu tipo
        $eventType = $request->input('event');

        // Processamento baseado no tipo de evento
        switch ($eventType) {

            case 'connection.update':
                return $this->handleConnectionStatus($request->all());

            case 'qrcode.updated':
                return $this->handleQrcodeUpdated($request->all());

            case 'messages.upsert':
                return $this->handleMessagesUpsert($request->all());

            case 'new.token':
                return $this->handleNewToken($request->all());

            case 'send.message':
                return $this->handleSendMessage($request->all());

            case 'messages.update':
                return $this->handleMessagesUpdate($request->all());

            case 'logout.instance':
                return $this->handleLogoutInstance($request->all());

            case 'remove.instance':
                return $this->handleRemoveInstance($request->all());

            case 'presence.update':
                return $this->handlePresenceUpdate($request->all());
            default:
                break;
        }
    }

    // Lida com o evento de Status da conexão do whatsapp
    private function handleConnectionStatus($data)
    {
        $instance = WhatsappInstance::where('name', $data['instance'])->first();

        // Verifica se a instância existe
        if (!$instance) {
            return; //caso nenhuma instância seja encontrada, aborta fluxo para não repetir as notificações
        }

        // Verifica se o estado atual da instância bate com o estado do webhook
        if ($data['data']['state'] === $instance->status) {
            return; //caso possua o mesmo status, aborta fluxo para não repetir as notificações
        }

        // Se o estado for Conectado, Desconectado ou Recusado, limpa o QR Code da tabela
        if ($data['data']['state'] === 'open' || $data['data']['state'] === 'close' || $data['data']['state'] === 'refused') {
            $instance->update(['qr_code' => null]);
        }

        // Atualiza o status da instância
        $instance->update(['status' => $data['data']['state']]);

        // Chamar serviço para atualizar a foto de perfil da instância
        if ($data['data']['state'] === 'open') {
            $fetchService = new FetchEvolutionInstanceService();
            $fetchService->fetchInstance($data['instance']);
        }

        // Busca o admin da organização
        $organization = Organization::find($instance->organization_id);
        $adminUser    = $organization->members()->where('is_tenant_admin', true)->first();

        // Traduz o status com o Enum de Conexão
        $stateLabel = StatusConnectionEnum::tryFrom($data['data']['state'])->getLabel();

        // Envia a notificação ao admin do tenant
        Notification::make()
            ->title('Status da Instância Atualizado')
            ->body("A instância {$data['instance']} teve seu status atualizado para {$stateLabel}.")
            ->sendToDatabase($adminUser);

    }
    // Lida com o evento de atualização do QrCode do whatsapp
    private function handleQrcodeUpdated($data)
    {
        // Verifica se há um erro no retorno do webhook
        if (isset($data['message']) && isset($data['statusCode'])) {
            Log::error("Erro no evento QRCODE_UPDATED: {$data['message']} (Código: {$data['statusCode']})");

            $instance = WhatsappInstance::where('name', $data['instance'] ?? null)->first();

            if ($instance) {
                $organization = Organization::find($instance->organization_id);

                if ($organization) {
                    $adminUser = $organization->members()->where('is_tenant_admin', true)->first();

                    if ($adminUser) {
                        Notification::make()
                            ->title('Erro ao Atualizar QR Code')
                            ->body("A instância {$data['instance']} encontrou um erro: {$data['message']}. Tente logar novamente.")
                            ->sendToDatabase($adminUser);
                    }
                }
            }

            return;
        }

        // Verifica se o webhook contém os dados necessários
        if (empty($data['data']['qrcode']['base64']) || empty($data['instance'])) {
            Log::warning('Evento QRCODE_UPDATED recebido com dados incompletos: ' . json_encode($data));

            return;
        }

        // Busca a instância de WhatsApp
        $instance = WhatsappInstance::where('name', $data['instance'])->first();

        if (!$instance) {
            Log::warning("Nenhuma instância encontrada para '{$data['instance']}' no evento QRCODE_UPDATED.");

            return;
        }

        // Atualiza o QR Code na instância
        $instance->update([
            'qr_code'      => $data['data']['qrcode']['base64'],
            'pairing_code' => $data['data']['qrcode']['pairingCode'] ?? '',
            'updated_at'   => now(),
        ]);

        Log::info("QR Code atualizado para {$data['data']['qrcode']['base64']}");

        // Busca a organização e o administrador da organização
        $organization = Organization::find($instance->organization_id);

        if (!$organization) {
            Log::warning("Nenhuma organização encontrada para a instância {$data['instance']}.");

            return;
        }

        $adminUser = $organization->members()->where('is_tenant_admin', true)->first();

        if (!$adminUser) {
            Log::warning("Nenhum administrador encontrado para a organização ID {$organization->id}.");

            return;
        }

        // Envia a notificação
        Notification::make()
            ->title('Novo QR Code Disponível')
            ->body("A instância {$data['instance']} gerou um novo QR Code. Utilize-o para autenticar sua conta.")
            ->sendToDatabase($adminUser);
    }

    // Lida com o evento MESSAGES_UPSERT
    private function handleMessagesUpsert($data)
    {

    }

    // Lida com o evento NEW_TOKEN
    private function handleNewToken($data)
    {

    }

    // Lida com o evento SEND_MESSAGE
    private function handleSendMessage($data)
    {

    }

    // Lida com o evento MESSAGES_UPDATE
    private function handleMessagesUpdate($data)
    {

    }

    // Lida com o evento LOGOUT_INSTANCE
    private function handleLogoutInstance($data)
    {

    }

    // Lida com o evento REMOVE_INSTANCE
    private function handleRemoveInstance($data)
    {

    }

    // Lida com o evento PRESENCE_UPDATE
    private function handlePresenceUpdate($data)
    {

    }

}
