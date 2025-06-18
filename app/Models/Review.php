<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_provider_id',
        'booking_id',
        'rating',
        'comment',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
} 