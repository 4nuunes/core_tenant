<?php

namespace App\Services\Evolution\Message;

use App\Services\Traits\EvolutionClientTrait;
use Exception;

class SendMessageEvolutionService
{
    use EvolutionClientTrait;
    public function sendMessage(string $record, array $data)
    {
        //dd($data, $record);

        try {
            $formattedNumber = preg_replace('/\D/', '', $data['number_whatsapp']);

            $payload = [
                'number' => $formattedNumber,
                'text'   => $data['message'],
            ];

            $response = $this->makeRequest("/message/sendText/{$record}", 'POST', $payload);

            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }

            return $response;

        } catch (Exception $e) {

            return ['error' => $e->getMessage()];
        }
    }
}
