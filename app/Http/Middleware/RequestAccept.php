<?php

namespace FreeradiusWeb\Http\Middleware;

use Closure;

class RequestAccept
{
    /**
     * Force a mime type as the first 'Accept' value in the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $mimeType
     * @return mixed
     */
    public function handle($request, Closure $next, $mimeType = 'application/json')
    {
        $mimeTypeFound = false;
        $acceptChanges = false;
        $accept = $request->headers->get('accept', [], false);

        foreach ($accept as &$media) {
            $parts = explode(';', $media);
            if ($mimeType === $parts[0]) {
                $mimeTypeFound = true;
                if (count($parts) > 1 && strpos($parts[1], 'q=1') === false) {
                    $acceptChanges = true;
                    $parts[1] = 'q=1';
                    $media = implode(';', $parts);
                }
                break;
            }
        }

        if (!$mimeTypeFound) {
            $acceptChanges = true;
            array_unshift($accept, $mimeType);
        }

        if ($acceptChanges) {
            $request->headers->set('accept', $accept);
        }

        return $next($request);
    }
}
