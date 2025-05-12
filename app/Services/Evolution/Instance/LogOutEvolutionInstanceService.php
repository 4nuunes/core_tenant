<?php

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;
use Exception;

class LogOutEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function logoutInstance(string $instanceId)
    {
        try {
            $response = $this->makeRequest("/instance/logout/{$instanceId}", 'DELETE');

            // Check if 'error' is present and truthy
            if (!empty($response['error'])) {
                throw new Exception($response['error']);
            }

            return $response;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
