<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_provider_id',
        'service_id',
        'scheduled_at',
        'ended_at',
        'notes',
        'status',
        'total_price',
        'payment_status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
} 