<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /* =========================
     * SHOW FORMS
     * ========================= */

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /* =========================
     * HANDLE LOGIN
     * ========================= */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password'])
                ->withInput();
        }

        $request->session()->regenerate();
        // store login timestamp for "logged in X minutes ago" display
        $request->session()->put('login_at', now()->toDateTimeString());

        return redirect()->intended(
            $this->redirectByRole(Auth::user())
        );
    }

    /* =========================
     * HANDLE REGISTRATION
     * ========================= */

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users'],
            'password'              => ['required', 'min:6', 'confirmed'],
        ]);

        // Default role = voter
        $voterRole = Role::where('name', 'election_admin')->firstOrFail();

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $voterRole->id,
        ]);

        return redirect('/login')->with('success', 'Account created. Please login.');
    }

    /* =========================
     * LOGOUT
     * ========================= */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /* =========================
     * FORGOT / RESET PASSWORD
     * ========================= */

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset(
            $data,
            function (User $user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('/login')->with('success', __($status));
        }

        return back()->withErrors(['email' => [__($status)]]);
    }

    /* =========================
     * ROLE-BASED REDIRECT
     * ========================= */

    private function redirectByRole(User $user): string
    {
        return match ($user->role->name) {
            'super_admin'   => '/admin',
            'election_admin'=> '/dashboard',
            'observer'      => '/results',
            'voter'         => '/vote',
            default         => '/dashboard',
        };
    }
}
