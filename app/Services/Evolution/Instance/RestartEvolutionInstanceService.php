<?php

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;
use Exception;

class RestartEvolutionInstanceService
{
    use EvolutionClientTrait;
    public function restartInstance(string $instanceId)
    {
        try {
            $response = $this->makeRequest("/instance/restart/{$instanceId}", 'POST');

            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }

            return $response;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
