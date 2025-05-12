<?php

namespace App\Models;

use App\Enums\TenantSuport\{TicketPriorityEnum, TicketStatusEnum, TicketTypeEnum};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'title',
        'description',
        'file',
        'image_path',
        'priority',
        'status',
        'type',
        'closed_at',
    ];

    protected $casts = [

        'type'       => TicketTypeEnum::class,
        'priority'   => TicketPriorityEnum::class,
        'status'     => TicketStatusEnum::class,
        'file'       => 'array',
        'created_at' => 'datetime',
        'closed_at'  => 'datetime',

    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticketresponses()
    {
        return $this->hasMany(TicketResponse::class, 'ticket_id');
    }
}
