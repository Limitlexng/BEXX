<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderIdCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'rider_id', 'partner_id', 'card_number', 'qr_code_path',
        'verification_url', 'issue_date', 'expiry_date', 'status', 'card_image_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && $this->expiry_date->isFuture();
    }
}
