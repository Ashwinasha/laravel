<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\VerifyEmail;

class VerificationController extends Controller
{
    public function verify($id, $code)
    {
        $user = User::findOrFail($id);

        if ($user->verification_code !== $code) {
            return redirect('/login')->with('error', 'Invalid verification code.');
        }

        if (Carbon::now()->greaterThan($user->verification_code_expires_at)) {
            return redirect('/login')->with('error', 'Verification code has expired.');
        }

        $user->email_verified_at = Carbon::now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        return redirect('/login')->with('status', 'Email verified successfully. You can now log in.');
    }

    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'User not found.');
        }

        if ($user->email_verified_at) {
            return redirect('/login')->with('status', 'Your email is already verified.');
        }

        // Generate a new verification code
        $verificationCode = Str::random(40);
        $user->verification_code = $verificationCode;
        $user->verification_code_expires_at = Carbon::now()->addMinutes(60); // Code expires in 60 minutes
        $user->save();

        // Send the verification email
        Mail::to($user->email)->send(new VerifyEmail($user));

        return redirect('/login')->with('status', 'A new verification email has been sent.');
    }
}
