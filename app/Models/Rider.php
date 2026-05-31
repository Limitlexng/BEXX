<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id', 'rider_id', 'first_name', 'last_name', 'email', 'phone',
        'photo', 'date_of_birth', 'address', 'city', 'state', 'nin', 'bvn',
        'license_number', 'license_expiry', 'emergency_contact_name',
        'emergency_contact_phone', 'status', 'compliance_score', 'performance_score',
        'total_earnings', 'total_deliveries', 'accident_count', 'violation_count',
        'background_check_passed', 'background_check_date', 'current_motorcycle_id', 'assignment_date',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'license_expiry' => 'date',
        'assignment_date' => 'date',
        'background_check_date' => 'date',
        'background_check_passed' => 'boolean',
        'compliance_score' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'total_earnings' => 'decimal:2',
    ];


    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function currentMotorcycle()
    {
        return $this->belongsTo(Motorcycle::class, 'current_motorcycle_id');
    }

    public function assignments()
    {
        return $this->hasMany(RiderAssignment::class);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function complianceRecords()
    {
        return $this->hasMany(ComplianceRecord::class);
    }

    public function idCards()
    {
        return $this->hasMany(RiderIdCard::class);
    }

    public function activeIdCard()
    {
        return $this->hasOne(RiderIdCard::class)->where('status', 'active')->latest();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getLicenseDaysRemainingAttribute(): ?int
    {
        if (!$this->license_expiry) return null;
        return now()->diffInDays($this->license_expiry, false);
    }
}
