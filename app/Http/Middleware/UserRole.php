<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Penting: import Auth facade

class UserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check() || Auth::user()->UserRole !== $role) {
            abort(403, 'Anda tidak memiliki hak akses yang diperlukan untuk halaman ini.');
        }

        return $next($request);
    }
}
