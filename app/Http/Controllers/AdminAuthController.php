<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials)) {
            return back()
                ->withErrors(['username' => 'Las credenciales proporcionadas no son válidas.'])
                ->onlyInput('username');
        }

        if (! Auth::guard('admin')->user()->estado) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->with('error', 'Tu cuenta se encuentra deshabilitada.')
                ->withInput($request->only('username'));
        }

        $request->session()->regenerate();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Has iniciado sesión correctamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }
}
