<?php

namespace App\Helpers;

use App\Helpers\Venturo;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AuthHelper
{
    use SoftDeletes;

    public static function login($email, $password)
    {
        try {
            $credentials = ['email' => $email, 'password' => $password];
            $user = UserModel::where('email', $email)->first();
            if (!$user) {
                return [
                    'status' => false,
                    'error' => ['Kombinasi email dan password yang kamu masukkan tidak ditemukan']
                ];
            }

            // Check if user status is active (status 1)
            if ($user->status !== 1) {
                return [
                    'status' => false,
                    'error' => ['Akun tidak aktif.']
                ];
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'status' => false,
                    'error' => ['Kombinasi email dan password yang kamu masukkan salah']
                ];
            }

            // DB::table('user_auth')
            //     ->where(['email' => $email])
            //     ->update(['updated_security' => Carbon::now()]);
        } catch (JWTException $e) {
            return [
                'status' => false,
                'error' => ['Could not create token.']
            ];
        }

        return [
            'status' => true,
            'data' => self::createNewToken($token)
        ];
    }

    protected static function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'updated_security' => (new UserResource(auth()->user()))->updated_security,
            'user' => new UserResource(auth()->user())
        ];
    }

    public static function generateTokenAndUpdateDatabase($email)
    {
        $token = Str::random(60);

        // $existingRecord = DB::table('password_resets')->where('email', $email)->first();

        // if ($existingRecord) {
        //     DB::table('password_resets')->where('email', $email)->update(['token' => $token, 'created_at' => now()]);
        // } else {
        //     DB::table('password_resets')->insert(['email' => $email, 'token' => $token, 'created_at' => now()]);
        // }

        DB::table('password_resets')->insert(['email' => $email, 'token' => $token, 'created_at' => now()]);

        return $token;
    }

    public static function updatePassword($email, $token, $newPassword)
    {
        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $email,
                'token' => $token
            ])
            ->first();

        if (!$updatePassword) {
            return false;
        }

        return UserModel::where('email', $email)
            ->update(['password' => Hash::make($newPassword)]);
    }

    public static function deleteResetToken($email, $token)
    {
        DB::table('password_resets')
            ->where(['email' => $email, 'token' => $token])
            ->update(['deleted_at' => Carbon::now()]);
    }
}
