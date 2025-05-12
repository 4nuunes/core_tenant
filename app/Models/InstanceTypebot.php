<?php

namespace App\Models;

use App\Enums\Evolution\Typebot\{TriggerOperatorEnum, TriggerTypeEnum};
use App\Services\Evolution\Typebot\CreateTypeBotService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class InstanceTypebot extends Model
{
    protected $fillable = [
        'whatsapp_instance_id',
        'name',
        'is_active',
        'url',
        'type_bot',
        'trigger_type',
        'trigger_operator',
        'trigger_value',
        'expire',
        'keyword_finish',
        'delay_message',
        'unknown_message',
        'listening_from_me',
        'stop_bot_from_me',
        'keep_open',
        'debounce_time',
        'id_typebot',
    ];

    protected $casts = [
        'trigger_operator'  => TriggerOperatorEnum::class,
        'trigger_type'      => TriggerTypeEnum::class,
        'is_active'         => 'boolean',
        'keep_open'         => 'boolean',
        'debounce_time'     => 'integer',
        'delay_message'     => 'integer',
        'expire'            => 'integer',
        'listening_from_me' => 'boolean',
        'stop_bot_from_me'  => 'boolean',
        'unknown_message'   => 'boolean',
    ];

    public function whatsappInstance(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstance::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($instanceTypebot) {

            $service  = new CreateTypeBotService();
            $response = $service->createTypeBot($instanceTypebot);

            if (isset($response['error'])) {
                Log::error("Erro ao criar TypeBot: " . $response['error']);
            }
        });
    }
}
