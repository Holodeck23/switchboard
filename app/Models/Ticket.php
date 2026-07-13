<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public const CATEGORIES = ['access', 'wifi', 'billing', 'cleaning', 'noise', 'other'];
    public const PRIORITIES = ['urgent', 'high', 'normal', 'low'];
    public const STATUSES = ['new', 'triaged', 'investigating', 'waiting', 'resolved'];

    protected $fillable = [
        'reservation_id', 'help_article_id', 'guest_email', 'channel', 'message',
        'category', 'priority', 'status', 'confidence', 'needs_escalation', 'draft_reply',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'integer',
            'needs_escalation' => 'boolean',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function helpArticle(): BelongsTo
    {
        return $this->belongsTo(HelpArticle::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class);
    }
}
