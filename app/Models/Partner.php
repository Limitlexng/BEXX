<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'partner_code', 'partner_type', 'company_name', 'contact_person',
        'phone', 'address', 'city', 'state', 'cac_number', 'tax_id', 'logo',
        'wallet_balance', 'pending_balance', 'total_withdrawn', 'lifetime_earnings',
        'bonus_earnings', 'status', 'bank_name', 'bank_account_number',
        'bank_account_name', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'lifetime_earnings' => 'decimal:2',
        'bonus_earnings' => 'decimal:2',
        'approved_at' => 'datetime',
        'purchase_date' => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function motorcycles()
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function riders()
    {
        return $this->hasMany(Rider::class);
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

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?? $this->contact_person ?? $this->user->name;
    }

    public function getActiveMotorcyclesCountAttribute(): int
    {
        return $this->motorcycles()->where('status', 'active')->count();
    }

    public function getActiveRidersCountAttribute(): int
    {
        return $this->riders()->where('status', 'active')->count();
    }

    public function getFleetUtilizationRateAttribute(): float
    {
        $total = $this->motorcycles()->count();
        if ($total === 0) return 0;
        $active = $this->motorcycles()->where('status', 'active')->count();
        return round(($active / $total) * 100, 1);
    }
}
