<?php

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;

class DeleteEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function deleteInstance(string $instanceID)
    {

        $response = $this->makeRequest("/instance/delete/{$instanceID}", 'DELETE');

        return $response;
    }
}
