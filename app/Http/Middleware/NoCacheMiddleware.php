<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoCacheMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request); // Mendapatkan response dari middleware berikutnya

        // Menambahkan header cache untuk menghindari cache
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response; // Kembalikan response yang sudah diubah
    }
}
