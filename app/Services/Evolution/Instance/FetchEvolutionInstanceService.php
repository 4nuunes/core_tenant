<?php

namespace App\Services\Evolution\Instance;

use App\Models\WhatsappInstance;
use App\Services\Traits\EvolutionClientTrait;
use Exception;

class FetchEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function fetchInstance(string $instanceID)
    {
        $response = $this->makeRequest("/instance/fetchInstances?instanceName={$instanceID}", 'GET');

        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }

        // Verifica se a resposta é um array e percorre para encontrar a instância correta
        if (is_array($response)) {
            foreach ($response as $instance) {
                if (isset($instance['name']) && $instance['name'] === $instanceID) {
                    WhatsappInstance::where('name', $instanceID)->update([
                        'profile_picture_url' => $instance['profilePicUrl'] ?? null,
                    ]);

                    return $instance;
                }
            }

        }

        // Caso não encontre a instância no array
        throw new Exception("Instância '{$instanceID}' não encontrada na resposta.");
    }
}
