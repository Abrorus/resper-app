<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private const PESAN_DIBLOKIR = 'Akun Anda diblokir sementara. Hubungi manajer.';

    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Akun terblokir tidak boleh login walaupun password-nya benar
        if ($user && ! $user->isActive()) {
            return back()->withErrors(['email' => self::PESAN_DIBLOKIR])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($user) {
                $user->increment('failed_login_attempts');

                if ($user->failed_login_attempts >= User::MAX_FAILED_LOGIN) {
                    $user->account_status = AccountStatus::Blocked;
                    $user->save();

                    return back()->withErrors(['email' => self::PESAN_DIBLOKIR])->onlyInput('email');
                }
            }

            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->user()->forceFill(['failed_login_attempts' => 0])->save();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}