<?php

namespace App\Services\Evolution\Typebot;

use App\Enums\Evolution\Typebot\TriggerOperatorEnum;
use App\Models\InstanceTypebot;
use App\Services\Traits\EvolutionClientTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateTypeBotService
{
    use EvolutionClientTrait;

    public function createTypeBot(InstanceTypebot $instanceTypebot)
    {
        try {

            $payload    = $this->buildPayload($instanceTypebot);
            $instanceId = $instanceTypebot->whatsappInstance->name ?? null;

            //dd($payload, $instanceId);
            $response = $this->makeRequest("/typebot/create/{$instanceId}", 'POST', $payload);

            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }

            // Verifica se o ID do Typebot foi retornado na resposta
            if (!empty($response['id'])) {
                $instanceTypebot->update(['id_typebot' => $response['id']]);
            } else {
                Log::warning("ID do Typebot não retornado na resposta da API.", ['response' => $response]);
            }

            return $response;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function buildPayload(InstanceTypebot $instanceTypebot): array
    {
        return [
            "enabled"         => true,
            "description"     => $instanceTypebot->name,
            "url"             => $instanceTypebot->url,
            "typebot"         => $instanceTypebot->type_bot,
            "triggerType"     => $instanceTypebot->trigger_type?->value, // Pegamos apenas o value do Enum
            "triggerOperator" => $this->formatTriggerOperator($instanceTypebot->trigger_operator),
            "triggerValue"    => $this->formatTriggerValue($instanceTypebot->trigger_value),
            "expire"          => (int) $instanceTypebot->expire,
            "keywordFinish"   => $instanceTypebot->keyword_finish,
            "delayMessage"    => (int) $instanceTypebot->delay_message,
            "unknownMessage"  => (string) $instanceTypebot->unknown_message, // Convertendo para string
            "listeningFromMe" => (bool) $instanceTypebot->listening_from_me,
            "stopBotFromMe"   => (bool) $instanceTypebot->stop_bot_from_me,
            "keepOpen"        => (bool) $instanceTypebot->keep_open,
            "debounceTime"    => (int) $instanceTypebot->debounce_time,
        ];
    }

    private function formatTriggerOperator($operator): string
    {
        $validOperators = ['equals', 'contains', 'startsWith', 'endsWith', 'regex'];

        if ($operator instanceof TriggerOperatorEnum) {
            return in_array($operator->value, $validOperators) ? $operator->value : 'contains';
        }

        return in_array($operator, $validOperators) ? $operator : 'contains';
    }

    private function formatTriggerValue($value): string
    {
        return is_null($value) ? '' : (string) $value;
    }
}
