<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attempt = Auth::attempt($request->all());

        if (!$attempt) {
            return $this->fail('Username atau password salah');
        }

        $user = User::find($request->username);
        $token = $user->createToken($request->username);

        return $this->success(null, [
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function register(Request $request)
    {
        try {
            $user = User::create($request->all());
            $token = $user->createToken($user->username);

            return $this->success(null, [
                'user' => $user,
                'token' => $token->plainTextToken
            ]);
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }
}
