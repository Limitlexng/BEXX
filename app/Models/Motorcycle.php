<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motorcycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id', 'fleet_id', 'vehicle_number', 'plate_number', 'vin_number',
        'engine_number', 'brand', 'model', 'year', 'color', 'purchase_date',
        'purchase_cost', 'insurance_provider', 'insurance_policy_number',
        'insurance_status', 'insurance_expiry', 'road_worthiness_expiry',
        'status', 'health_rating', 'health_score', 'total_earnings',
        'total_maintenance_cost', 'current_location', 'latitude', 'longitude',
        'last_seen_at', 'photos', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'insurance_expiry' => 'date',
        'road_worthiness_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_maintenance_cost' => 'decimal:2',
        'health_score' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_seen_at' => 'datetime',
        'photos' => 'array',
    ];


    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function riders()
    {
        return $this->hasMany(Rider::class, 'current_motorcycle_id');
    }

    public function currentRider()
    {
        return $this->hasOne(Rider::class, 'current_motorcycle_id');
    }

    public function assignments()
    {
        return $this->hasMany(RiderAssignment::class);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function complianceRecords()
    {
        return $this->hasMany(ComplianceRecord::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->brand} {$this->model}";
    }

    public function getInsuranceDaysRemainingAttribute(): ?int
    {
        if (!$this->insurance_expiry) return null;
        return now()->diffInDays($this->insurance_expiry, false);
    }

    public function getRoiAttribute(): float
    {
        if (!$this->purchase_cost || $this->purchase_cost == 0) return 0;
        return round(($this->total_earnings / $this->purchase_cost) * 100, 2);
    }
}
