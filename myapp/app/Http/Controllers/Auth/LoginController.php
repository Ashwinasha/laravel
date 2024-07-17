<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validator($request->all())->validate();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'email_verified_at' => null])) {
            return redirect()->back()->with('error', 'You need to verify your email before logging in.');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended('home');
        }

        return redirect()->back()->with('error', 'Invalid credentials.');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
