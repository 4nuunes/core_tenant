<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Http;

trait EvolutionClientTrait
{
    protected string $apiKey;

    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.evolution.key');
        $this->apiUrl = config('services.evolution.url');
    }

    protected function makeRequest(string $endpoint, string $method = 'GET', array $data = [])
    {
        // Use a forma explícita de cada tipo de request
        $httpRequest = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey'       => $this->apiKey,
        ]);

        // Verificar o método HTTP e fazer a requisição de forma apropriada
        switch (strtoupper($method)) {
            case 'POST':
                $response = $httpRequest->post($this->apiUrl . $endpoint, $data);

                break;
            case 'PUT':
                $response = $httpRequest->put($this->apiUrl . $endpoint, $data);

                break;
            case 'DELETE':
                $response = $httpRequest->delete($this->apiUrl . $endpoint, $data);

                break;
            case 'GET':
            default:
                $response = $httpRequest->get($this->apiUrl . $endpoint, $data);

                break;
        }

        // Retornar a resposta como JSON
        return $response->json();
    }
}
