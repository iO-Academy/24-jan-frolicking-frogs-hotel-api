<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'customer' => 'required|string|max:100',
            'guests' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        return $next($request);
    }
}
