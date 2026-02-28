<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminSetupController extends Controller
{
    public function showCreateForm()
    {
        if ($this->adminExists()) {
            return redirect()->route('login')->withErrors([
                'email' => 'An admin account already exists. Please sign in.',
            ]);
        }

        return view('auth.create-admin');
    }

    public function create(Request $request)
    {
        if ($this->adminExists()) {
            return redirect()->route('login')->withErrors([
                'email' => 'An admin account already exists. Please sign in.',
            ]);
        }

        $data = $request->validate([
            'email' => 'required|email|unique:system_users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = SystemUser::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    private function adminExists(): bool
    {
        return SystemUser::where('role', 'admin')->exists();
    }
}
