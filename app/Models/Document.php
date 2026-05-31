<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id', 'rider_id', 'motorcycle_id', 'type', 'title',
        'file_path', 'file_name', 'file_type', 'file_size', 'issue_date',
        'expiry_date', 'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'file_size' => 'integer',
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
