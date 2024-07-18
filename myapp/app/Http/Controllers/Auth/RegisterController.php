<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\VerifyEmail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Generate verification code and expiration time
        $verificationCode = Str::random(40);
        $user->verification_code = $verificationCode;
        $user->verification_code_expires_at = Carbon::now()->addMinutes(60); // Code expires in 60 minutes
        $user->save();

        // Send verification email
        Mail::to($user->email)->send(new VerifyEmail($user));

        return redirect('/login')->with('status', 'Please check your email for a verification link.');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => null,
            'verification_code' => null, // Initial value
            'verification_code_expires_at' => null, // Initial value
        ]);
    }
}
