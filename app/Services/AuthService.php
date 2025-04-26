<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'   =>  1
        ]);
    }

    // public function login(array $credentials)
    // {
    //     $user = User::where('email', $credentials['email'])->first();

    //     if (!$user || !Hash::check($credentials['password'], $user->password)) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The provided credentials are incorrect.'],
    //         ]);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return [
    //         'user' => $user,
    //         'token' => $token,
    //     ];
    // }

    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // 1. Create the token
        $tokenResult = $user->createToken('auth_token');
        $plainTextToken = $tokenResult->plainTextToken;

        // 2. Retrieve the "accessToken" record
        $accessToken = $tokenResult->accessToken;

        // 3. Set the expiration date (for example, 7 days)
        $accessToken->expires_at = now()->addDays(7);
        $accessToken->save();

        return [
            'user'  => $user,
            'token' => $plainTextToken,
        ];
    }

    public function delegateAccess(array $credentials)
    {
        $user = User::find($credentials['id']);

        // 1. Create the token
        $tokenResult = $user->createToken('auth_token');
        $plainTextToken = $tokenResult->plainTextToken;

        // 2. Retrieve the "accessToken" record
        $accessToken = $tokenResult->accessToken;

        // 3. Set the expiration date (for example, 7 days)
        $accessToken->expires_at = now()->addDays(7);
        $accessToken->save();

        return [
            'user'  => $user,
            'token' => $plainTextToken,
        ];
    }


    public function logout($user)
    {
        $user->tokens()->delete();
    }
}