<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1️⃣ authenticate (email + password)
        $request->authenticate();

        // 2️⃣ regenerate session
        $request->session()->regenerate();

        // 3️⃣ ✅ เช็กสถานะผู้ใช้งาน
        if (auth()->user()->status !== 'active') {

            // logout ทันที
            Auth::guard('web')->logout();

            // ล้าง session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // ส่ง error กลับหน้า login
            return back()->withErrors([
                'email' => 'บัญชีนี้ถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบ',
            ]);
        }

        // 4️⃣ ผ่านทุกอย่าง → เข้าใช้งานได้
        return redirect()->intended('/documents');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
