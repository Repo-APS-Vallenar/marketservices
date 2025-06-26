<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Importante para que hasRole() funcione

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles; // Este trait de Spatie añade el método hasRole() y otros.

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Asegúrate de que 'role' esté aquí si lo usas para roles básicos
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'string',
        ];
    }

    /**
     * Get the bookings for the customer.
     * Relación para obtener las reservas de un usuario (cuando actúa como cliente).
     */
    public function bookings() // Nombre de la relación
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function serviceProvider()
    {
        return $this->hasOne(\App\Models\ServiceProvider::class);
    }
}
