<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:admin,staff',
        ]);

        $email = $request->email;
        $password = $request->password;
        $role = $request->role;

        // Find user by email and role
        $user = SystemUser::where('email', $email)
            ->where('role', $role)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid login credentials.'
            ])->withInput($request->except('password'));
        }

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on role
        return $role === 'admin'
            ? redirect()->route('dashboard') // or admin-specific dashboard
            : redirect()->route('dashboard'); // or staff-specific dashboard
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
