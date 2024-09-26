<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Helpers\AuthHelper;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $user;
    private $auth;

    public function __construct()
    {
        $this->user = new UserHelper();
        $this->auth = new AuthHelper();
    }

    public function login(AuthRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        $login       = AuthHelper::login($credentials['email'], $credentials['password']);

        if (!$login['status']) {
            return response()->failed($login['error'], 422);
        }

        return response()->success($login['data']);
    }

    // public function submitForgetPasswordForm(ForgotPasswordRequest $request)
    // {
    //     $email = $request->email;

    //     $token = AuthHelper::generateTokenAndUpdateDatabase($email);

    //     Mail::send('generate.auth.forgot-password', ['token' => $token, 'email' => $email], function ($message) use ($email) {
    //         $message->to($email)->subject('Reset Your Password');
    //     });

    //     return response()->success(['status' => 'Email sent successfully']);
    // }

    public function submitForgetPasswordForm(ForgotPasswordRequest $request)
    {
        $email = $request->email;

        $user = UserModel::where('email', $email)->first();

        if (!$user) {
            return response()->failed(['error' => 'Email yang kamu masukkan tidak ditemukan'], 422);
        }

        $token = AuthHelper::generateTokenAndUpdateDatabase($email);

        Mail::send('generate.auth.forgot-password', ['token' => $token, 'email' => $email], function ($message) use ($email) {
            $message->to($email)->subject('Reset Your Password');
        });

        return response()->success(['message' => 'Email berhasil dikirim']);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('generate.auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function submitResetPasswordForm(ResetPasswordRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        // $payload = $request->only(['email', 'token', 'password']);

        $email = $request->email;
        $token = $request->token;
        $newPassword = $request->password;

        $passwordUpdated = $this->auth->updatePassword($email, $token, $newPassword);

        if (!$passwordUpdated) {
            return response()->failed('Invalid token!');
        }

        AuthHelper::deleteResetToken($email, $token);

        return response()->success(['message' => 'Password anda telah diubah!']);
    }

    public function profile()
    {
        return response()->success(new UserResource(auth()->user()));
    }
}
