<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_id',
        'category_id',
        'name',
        'description',
        'price',
        'price_unit',
        'estimated_duration',
    ];

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
} 