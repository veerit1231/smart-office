<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // ถ้า login อยู่ แต่ status ไม่ active
        if (Auth::check() && Auth::user()->status !== 'active') {

            // logout ทันที
            Auth::guard('web')->logout();

            // ล้าง session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // กลับไปหน้า login พร้อม error
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'บัญชีของคุณถูกปิดการใช้งาน',
                ]);
        }

        return $next($request);
    }
}
