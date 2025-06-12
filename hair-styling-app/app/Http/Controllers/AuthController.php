<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = Hash::make($request->password);
        $customer->photo = "";
        $customer->is_admin = true;

        if ($customer->save()) {
            Auth::guard('customer')->login($customer);
            return redirect()->route('admin.dashboard');
        } else {
            return back()->withErrors(['email' => 'Помилка реєстрації. Спробуйте ще раз.']);
        }
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/login');
    }

    public function showLogin()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            if (Auth::guard('customer')->user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Невірний email або пароль.']);
    }
}
