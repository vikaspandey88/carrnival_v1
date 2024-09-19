<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Hash;

class AdminController extends Controller
{
    // Admin login form
    public function loginForm()
    {
        // Check if the admin is already authenticated
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard'); // Redirect to the dashboard if logged in
        }
        return view('admin.login');
    }

    // Handle admin login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
       // dd(Auth::guard('admin')->attempt($credentials));
        
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // Admin registration form
    public function registerForm()
    {
        return view('admin.register');
    }

    // Handle admin registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login')->with('success', 'Admin registered successfully.');
    }

    // Admin dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Admin logout
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }
}
