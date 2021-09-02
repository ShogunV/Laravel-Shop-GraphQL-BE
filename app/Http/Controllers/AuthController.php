<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->assignRole('customer');

        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('token', $token, 2880);

        return response([
            'token' => $token,
            'user' => $user
        ], Response::HTTP_CREATED)->withCookie($cookie);
    }

    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->only('email', 'password'))){
            return response([
                'error' => true,
                'data' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $user['role'] = $user->hasRole('admin') ? 'admin' : 'customer';

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('token', $token, 2880);

        return response([
            'token' => $token,
            'user' => $user
        ], Response::HTTP_OK)->withCookie($cookie);
    }

    public function logout()
    {
        $cookie = Cookie::forget('token');

        return response([
            'error' => false,
            'data' => 'Success'
        ], Response::HTTP_OK)->withCookie($cookie);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response([
            'error' => false,
            'data' => 'If that email address is in our database, we will send you an email to reset your password.'
        ], Response::HTTP_OK);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET ? response([
            'error' => false,
            'data' => 'Your password has been reset'
        ], Response::HTTP_OK) : response([
            'error' => true,
            'data' => 'There was an error while resetting password.'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function user(Request $request)
    {
        return Auth::user();
    }
}
