<?php

namespace App\Http\Controllers;

use App\Helpers\AuthCustomerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\AuthCustomer\AuthRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\User\UserResource;
use App\Models\CustomerModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthCustomerController extends Controller
{
    private $auth;

    public function __construct()
    {
        $this->auth = new AuthCustomerHelper();
    }

    public function login(AuthRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        $login       = AuthCustomerHelper::login($credentials['email'], $credentials['password']);
        // $login       = $this->auth->login($credentials['email'], $credentials['password']);

        if (!$login['status']) {
            return response()->failed($login['error'], 422);
        }
        
        return response()->success($login['data']);
    }

    public function submitForgetPasswordForm(ForgotPasswordRequest $request)
    {
        $email = $request->email;

        $user = CustomerModel::where('email', $email)->first();

        if (!$user) {
            return response()->failed(['error' => 'Email yang kamu masukkan tidak ditemukan'], 422);
        }

        $token = AuthCustomerHelper::generateTokenAndUpdateDatabase($email);

        Mail::send('generate.auth.forgot-password-customer', ['token' => $token, 'email' => $email, 'name' => $user->name], function ($message) use ($email) {
            $message->to($email)->subject('Reset Your Password');
        });

        return response()->success(['message' => 'Email berhasil dikirim']);
    }

    public function getTokenCustomer(Request $request)
    {
        $token = $request->token;
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->whereNull('deleted_at')
            ->first();

        if (!$reset) {
            return response()->json(['error' => 'Token reset password tidak valid'], 404);
        }

        $customer = CustomerModel::where('email', $reset->email)->first();

        if (!($customer['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => (new CustomerResource($customer))->email,
            'message' => 'Email berhasil dikirim'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6',
        ]);

        $resetedPassword = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();
        if (!$resetedPassword) {
            return response()->json(['error' => 'Data Token tidak ditemukan'], 404);
        }
        $customer = CustomerModel::where('email', $resetedPassword->email)->first();
        $user = UserModel::where('email', $resetedPassword->email)->first();

        if (!$customer || !$user) {
            return response()->json(['error' => 'Data user tidak ditemukan'], 404);
        }

        $customer->password = Hash::make($request->password);
        $user->password = $customer->password;
        $customer->save();
        $user->save();

        DB::table('password_resets')
            ->where('token', $request->token)
            ->update(['deleted_at' => now()]);

        // Berikan respons sukses
        return response()->json(['message' => 'Password berhasil diperbarui'], 200);
    }

    public function kirimOtp(Request $request)
    {
        $email = $request->email;

        $user = CustomerModel::where('email', $email)->first();

        if (!$user) {
            return response()->failed(['error' => 'Email yang kamu masukkan tidak ditemukan'], 422);
        }

        $otp = AuthCustomerHelper::generateOtp($email);

        Mail::send('generate.auth.otp-customer', ['otp' => $otp, 'email' => $email, 'name' => $user->name], function ($message) use ($email) {
            $message->to($email)->subject('Code OTP');
        });

        return response()->success(['message' => 'Email berhasil dikirim']);
    }

    public function getEmailOtpCustomer(Request $request)
    {
        $email = $request->email;
        $otp = DB::table('otp_request')
            ->where('email', $email)
            ->whereNull('deleted_at')
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'Kode OTP tidak valid'], 404);
        }

        $customer = CustomerModel::where('email', $otp->email)->first();

        if (!($customer['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Email Terdaftar']);
    }

    public function verifyOtp(Request $request)
    {
        // dd($request->otp);
        $otp = DB::table('otp_request')
            ->where('email', $request->email)
            ->whereNull('deleted_at')
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // Verifikasi OTP
        if (Hash::check($request->otp, $otp->otp)) {
            // OTP cocok
            // Lanjutkan dengan tindakan yang diperlukan
            DB::table('otp_request')
                ->where('email', $request->email)
                ->where('otp', $otp->otp)
                ->update(['deleted_at' => now()]);

            return response()->json(['message' => 'OTP verified successfully']);
        } else {
            // OTP tidak cocok
            return response()->json(['error' => 'Kode OTP tidak sesuai'], 400);
        }
    }

    public function profile()
    {
        $user = auth()->user();
    
        $customer = CustomerModel::where('email', $user->email)->first();
    
        if ($customer) {
            return response()->success(new CustomerResource($customer));
        } else {
            return response()->error('Customer not found', 404);
        }
    }
}
