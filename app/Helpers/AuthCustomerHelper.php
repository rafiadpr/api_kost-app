<?php

namespace App\Helpers;

use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\User\UserResource;
use App\Models\CustomerModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthCustomerHelper
{
    public static function updateSecurity($customerId)
    {
        try {
            $user = UserModel::findOrFail($customerId);
            $user->updated_security = now();
            $user->save();

            return true;
        } catch (\Exception $e) {

            return false;
        }
    }

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
        } catch (JWTException $e) {
            return [
                'status' => false,
                'error' => ['Could not create token.']
            ];
        }

        // AuthCustomerHelper::updateSecurity(auth()->user()->id);

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

        $existingReset = DB::table('password_resets')
            ->where('email', $email)
            ->whereNull('deleted_at')
            ->first();

        // Jika sudah ada record dengan email yang sesuai, update token dan waktu pembuatan baru
        if ($existingReset) {
            DB::table('password_resets')
                ->where('email', $email)
                ->update(['token' => $token, 'created_at' => now()]);
        } else {
            // Jika tidak ada record dengan email yang sesuai, insert data baru
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]);
        }

        return $token;
    }

    public static function generateOtp($email)
    {
        $otp = mt_rand(10000, 99999);
        $hashOtp = Hash::make($otp);
        
        $existingOtp = DB::table('otp_request')
            ->where('email', $email)
            ->whereNull('deleted_at')
            ->first();

        // Jika sudah ada record dengan email yang sesuai, update token dan waktu pembuatan baru
        if ($existingOtp) {
            DB::table('otp_request')
                ->where('email', $email)
                ->whereNull('deleted_at')
                ->update(['otp' => $hashOtp, 'created_at' => now()]);
        } else {
            // Jika tidak ada record dengan email yang sesuai, insert data baru
            DB::table('otp_request')->insert([
                'email' => $email,
                'otp' => $hashOtp,
                'created_at' => now()
            ]);
        }

        return $otp;
    }
}
