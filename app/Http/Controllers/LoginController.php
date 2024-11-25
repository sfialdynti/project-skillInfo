<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Login
    public function login()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        $validate = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ], [
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Please enter a valid email',
            'password.required' => 'Password cannot be empty'
        ]);

        if (Auth::attempt($validate)) {
            $request->session()->regenerate();
            if (Auth::user()->role == 'Admin') {
                return redirect('/dashboard');
            } elseif (Auth::user()->role == 'Assessor') {
                return redirect('/dashboardAssessor');
            } elseif (Auth::user()->role == 'Student') {
                return redirect('/dashboardStudent');
            } else {
                return redirect('/');
            }
            // return redirect('/dashboard');
        }

        return redirect()->back()->with('statusLogin', 'Maaf Login anda gagal, Email atau Password yang dimasukkan salah');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
