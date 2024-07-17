<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($id)
    {
        $user = User::find($id);
        if ($user && $user->email_verified_at == null) {
            $user->email_verified_at = now();
            $user->save();
            return redirect('/login')->with('status', 'Email verified successfully.');
        }
        return redirect('/login')->with('error', 'Invalid verification link or email already verified.');
    }
}

