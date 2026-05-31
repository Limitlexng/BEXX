<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id', 'rider_id', 'partner_id', 'assigned_date',
        'unassigned_date', 'status', 'notes', 'earnings_during_assignment',
        'deliveries_during_assignment',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'unassigned_date' => 'date',
        'earnings_during_assignment' => 'decimal:2',
    ];

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
