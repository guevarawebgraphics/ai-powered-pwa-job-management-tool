<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Validator;
use Hash;
use Str;

class PageController extends Controller
{
    public function sendForgotPassword(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid email. Please try again.'], 422);
        }

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset password link successfully sent!'], 200);
        }

        return response()->json(['message' => 'Failed to send reset link. Please try again later.'], 500);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // Force reload the user from the database to avoid stale data
                $user = User::where('email', $user->email)->firstOrFail();

                // Update the password
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Logout all sessions by updating `remember_token`
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful!'], 200);
        }

        return response()->json(['message' => 'Invalid token or email. Please request a new reset link.'], 422);
    }
}
