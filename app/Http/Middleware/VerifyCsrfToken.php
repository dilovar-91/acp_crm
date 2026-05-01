<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'upload',
        'quiz/*',
        'events/*',
        'call/*',
        'events/summary',
        'events/recording',
        'events/record/added',
        'mango/*',
        'api/fares/upload',
        'jivo/*',
    ];
}
