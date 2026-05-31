<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id', 'partner_id', 'type', 'description', 'details',
        'workshop_name', 'technician_name', 'cost', 'service_date',
        'next_service_due', 'downtime_hours', 'status', 'parts_replaced', 'attachments',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_due' => 'date',
        'cost' => 'decimal:2',
        'parts_replaced' => 'array',
        'attachments' => 'array',
    ];

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
