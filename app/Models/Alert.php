<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id', 'type', 'title', 'message', 'severity',
        'alertable_type', 'alertable_id', 'read', 'read_at',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function alertable()
    {
        return $this->morphTo();
    }

    public function markAsRead(): void
    {
        $this->update(['read' => true, 'read_at' => now()]);
    }
}
