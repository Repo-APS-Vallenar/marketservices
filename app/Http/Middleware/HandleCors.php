<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\HandleCors as Middleware;

class HandleCors extends Middleware
{
    /**
     * The names of headers that should be added to the response.
     *
     * @var array<string>
     */
    protected $addedHeaders = [
        'Access-Control-Allow-Credentials' => 'true',
    ];
}