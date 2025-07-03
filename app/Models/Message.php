<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message_content', // Asegúrate de que 'message_content' esté aquí
        'read_at',         // Asegúrate de que 'read_at' esté aquí
    ];

    /**
     * Las relaciones que deberían ser precargadas por defecto en las consultas.
     * Esto no es estrictamente necesario para el error actual, pero es una buena práctica.
     *
     * @var array
     */
    protected $with = ['sender', 'recipient'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime', // Asegura que 'read_at' se maneje como un objeto DateTime
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the message.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
