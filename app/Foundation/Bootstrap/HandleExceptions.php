<?php

namespace App\Foundation\Bootstrap;

use Illuminate\Foundation\Bootstrap\HandleExceptions as LaravelHandleExceptions;

class HandleExceptions extends LaravelHandleExceptions
{
    /**
     * Laravel 8 is not PHP 8.4 compatible. Logging implicit-nullable
     * deprecations from Illuminate\Log\Logger recurses back into LogManager
     * and crashes Redis queue workers.
     *
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @return void
     */
    public function handleDeprecation($message, $file, $line)
    {
        if (str_contains((string) $file, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)) {
            return;
        }

        parent::handleDeprecation($message, $file, $line);
    }
}
