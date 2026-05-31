<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id', 'motorcycle_id', 'rider_id', 'amount', 'platform_fee',
        'net_amount', 'earning_date', 'period_type', 'reference', 'source', 'status', 'notes',
    ];

    protected $casts = [
        'earning_date' => 'date',
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }
}
