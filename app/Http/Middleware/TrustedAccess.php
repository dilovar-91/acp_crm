<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TrustedAccess
{

    private $trustedClients;

    public function __construct()
    {
        $this->trustedClients = [];
        if (env('TRUSTED_CLIENTS')) {
            $list = explode('|', env('TRUSTED_CLIENTS'));

            if ($list !== false && !empty($list)) {
                $this->trustedClients = $list;
                //Log::emergency($this->trustedClients);
            }
        }
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(env('APP_ENV')=='production') {
            $clientHostname = gethostbyaddr($request->getClientIp());
            //Log::emergency($request->getClientIp());

            //$clientHostname = $request->getRequestUri();
            Log::emergency($clientHostname);
            Log::emergency(request()->getHttpHost());
            $isTrusted = false;
            foreach ($this->trustedClients as $trustedClientHostname) {
                Log::emergency(self::isHostnameMatch($clientHostname, $trustedClientHostname));
                if (self::isHostnameMatch($clientHostname, $trustedClientHostname)) {
                    $isTrusted = true;
                    break;
                }
            }
            if ($isTrusted) {
                return $next($request);
            } else {
                return response()->json(['error'=>'Domain untrusted'], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $next($request);
        }
    }

    private static function isHostnameMatch($hostname, $trustedHostname)
    {
        if ($trustedHostname == '*' || $hostname == $trustedHostname) {
            return true;
        }
        // check if wildcard subdomain
        $subdomainDelimiterPosition = strpos($hostname, '.');
        if(strlen($trustedHostname) < 2 || $subdomainDelimiterPosition === false) {
            return false;
        }
        if (substr($trustedHostname, 0, 2) == '*.') {
            if (substr($trustedHostname, 1) == substr($hostname, $subdomainDelimiterPosition)) {
                return true;
            }
        }
        return false;
    }
}
