<?php

namespace App\Services\Evolution\Instance;

use App\Models\WhatsappInstance;
use App\Services\Traits\EvolutionClientTrait;
use Exception;

class ConnectEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function connectInstance(string $instanceId)
    {
        try {
            $response = $this->makeRequest("/instance/connect/{$instanceId}", 'GET');

            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }

            // Atualizar o banco com os dados retornados

            WhatsappInstance::where('name', $instanceId)->update([
                'qr_code'      => $response['base64'] ?? null,
                'count'        => $response['count'] ?? null,
                'pairing_code' => $response['pairingCode'] ?? null,
            ]);

            return $response;

        } catch (Exception $e) {

            return ['error' => $e->getMessage()];
        }
    }
}
