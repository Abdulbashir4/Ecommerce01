<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login() { return view('auth.login'); }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            return back()->withErrors(['phone' => 'Too many failed attempts. Please try again later.'])->onlyInput('phone');
        }

        if (!$user || !Hash::check($data['password'], $user->password)) {
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;
                $user->update([
                    'failed_login_attempts' => $attempts,
                    'locked_until' => $attempts >= 5 ? now()->addMinutes(15) : null,
                ]);
            }
            return back()->withErrors(['phone' => 'Invalid phone number or password.'])->onlyInput('phone');
        }

        if (!$user->isActive()) {
            return back()->withErrors(['phone' => 'Your account is not active. Please contact an administrator.'])->onlyInput('phone');
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        if ($user->force_password_change) {
            return redirect()->route('account.password.edit')->with('warning', 'Please change your password before continuing.');
        }

        return $user->isSuperAdmin() || $user->hasPermission('admin.access')
            ? redirect()->intended('/admin')
            : redirect()->intended('/account');
    }

    public function register() { return view('auth.register'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
            'status' => 'active',
        ]);

        // Customer role is assigned by RbacSeeder; this fallback keeps registration safe if the seed has not run yet.
        $customerRole = \App\Models\Role::where('slug', 'customer')->first();
        if ($customerRole) $user->roles()->syncWithoutDetaching([$customerRole->id]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/account');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
