<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function showCustomerLogin()
    {
        return view('auth.customer-login');
    }

    public function showCustomerRegister()
    {
        return view('auth.customer-register');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Unauthorized access']);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function customerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            if (!Auth::user()->isAdmin()) {
                return redirect()->route('customer.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Please use admin login']);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function customerRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        Auth::login($user);
        return redirect()->route('customer.dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}