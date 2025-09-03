<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\User;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
//use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Carbon\Carbon;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
//use App\Http\Controllers\Api\Mail;
//use App\Http\Controllers\Api\Carbon;
//use App\Http\Controllers\Auth;




class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'email', 'max:255', 'unique:' . User::class],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //     ], [], [
    //         'name' => 'Name',
    //         'email' => 'Email',
    //         'password' => 'Password',
    //     ]);
    //     if ($validator->fails()) {
    //         return ApiResponse::sendResponse(422, 'Register validation Errors', $validator->messages()->all());
    //     }
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     $data['token'] = $user->createToken('Telemarketing-crm')->plainTextToken;


    //     $data['name'] = $user->name;
    //     $data['email'] = $user->email;

    //     return ApiResponse::sendResponse(201, 'User Account Created Successefly', $data);
    // }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {

            return ApiResponse::sendResponse(401, 'username or password not correct', []);
        }

        // Optional: If you want to block a specific user (for example, not active), check here.

        $token = $user->createToken('Telemarketing-crm')->plainTextToken;
        $user = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(), // ['admin'] for example
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
        return ApiResponse::sendResponse(201, 'login succefully', $user);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        // Delet the current token
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::sendResponse(204, 'logged out successfully', []);
    }
    // send OTP
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Save OTP in DB
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send mail
        Mail::to($user->email)->send(new OtpMail($otp));
        return ApiResponse::sendResponse(200, 'OTP sent to your email', []);
    }


    // Verify OTP (optional before reset)
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        // Define the variable before use
        $otp = $request->input('otp');

        $user = User::where('email', $request->email)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();

        if (! $user) {

            return ApiResponse::sendResponse(400, 'Invalid or expired OTP', []);
        }
        return ApiResponse::sendResponse(200, 'OTP verified successfully', []);
    }


    // Reset password using OTP
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return ApiResponse::sendResponse(400, 'Invalid or expired OTP', []);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;

        $user->save();
        return ApiResponse::sendResponse(200, 'Password reset successfully', []);
    }
}
