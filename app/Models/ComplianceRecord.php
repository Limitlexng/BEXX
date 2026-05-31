<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id', 'rider_id', 'motorcycle_id', 'type', 'title',
        'description', 'issue_date', 'expiry_date', 'status', 'fine_amount',
        'resolved', 'resolution_notes', 'documents',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'fine_amount' => 'decimal:2',
        'resolved' => 'boolean',
        'documents' => 'array',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
