<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OTPMail;
use App\Http\Controllers\MailController;
use Exception;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['register', 'login', 'forgotPassword', 'registerAndroid', 'resetPassword', 'forgotPasswordVerifyToken']);
    }

    function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id_role' => 'required',
            'id_province' => 'required',
            'id_city' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'owner_name' => 'required',
            'umkm_name' => 'required',
            'umkm_description' => 'required',
            'instagram' => 'required',
            'whatsapp' => 'required',
            'facebook' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
            'umkm_image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = new User();
        $user->id_role = $req->input('id_role');
        $user->id_province = $req->input('id_province');
        $user->id_city = $req->input('id_city');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->owner_name = $req->input('owner_name');
        $user->umkm_name = $req->input('umkm_name');
        $user->umkm_description = $req->input('umkm_description');
        $user->instagram = $req->input('instagram');
        $user->whatsapp = $req->input('whatsapp');
        $user->facebook = $req->input('facebook');
        $user->address = $req->input('address');
        $user->phone_number = $req->input('phone_number');
        $user->umkm_image = $req->input('umkm_image');

        $result = $user->save();

        $token = JWTAuth::fromUser($user);

        if ($result) {
            return response()->json([
                'message' => 'Register successful',
                'data' => $user,
                'jwt token' => $token
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed to register account',
                'data' => null
            ], 400);
        }
    }

    public function registerAndroid(Request $req)
    {
        $validator = Validator::make($req->all(), [
            // 'id_role' => 'required',
            // 'id_province' => 'required',
            // 'id_city' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'owner_name' => 'required',
            'umkm_name' => 'required',
            // 'umkm_description' => 'required',
            // 'instagram' => 'required',
            // 'whatsapp' => 'required',
            // 'facebook' => 'required',
            // 'address' => 'required',
            // 'phone_number' => 'required',
            // 'umkm_image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = new User();
        $user->id_role = 1; //$req->input('id_role');
        $user->id_province = 1; //$req->input('id_province');
        $user->id_city = 1; //$req->input('id_city');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->owner_name = $req->input('owner_name');
        $user->umkm_name = $req->input('umkm_name');
        $user->umkm_description = ""; //$req->input('umkm_description');
        $user->instagram = ""; //$req->input('instagram');
        $user->whatsapp = ""; //$req->input('whatsapp');
        $user->facebook = ""; //$req->input('facebook');
        $user->address = ""; //$req->input('address');
        $user->phone_number = ""; //$req->input('phone_number');
        $user->umkm_image = ""; //$req->input('umkm_image');

        $result = $user->save();

        $token = JWTAuth::fromUser($user);

        if ($result) {
            return response()->json([
                'message' => 'Register successful',
                'data' => $user,
                'jwt token' => $token
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed to register account',
                'data' => null
            ], 400);
        }
    }

    function login(Request $req)
    {
        $user = User::where('email', $req->email)->first();
        if (!$user || !Hash::check($req->password, $user->password)) {
            return response([
                'message' => 'email/username not found, Invalid, check email or password',
                'data' => null
            ], 400);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        $credentials = $req->only('email', 'password');
        if (!$jwt_token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'email/username not found, Invalid, check email or password',
                'data' => null
            ], 400);
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => auth()->guard('api')->user(),
                'jwt_token' => $jwt_token
            ]
        ], 200);
    }

    function changePassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5|confirmed',
            'new_password_confirmation' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = Auth::user();
        if (!Hash::check($req->old_password, $user->password)) {
            return response([
                'message' => 'Old password is incorrect',
                'data' => null
            ], 400);
        }

        $user->password = Hash::make($req->new_password);
        $result = $user->save();

        if ($result) {
            return response()->json([
                'message' => 'Password changed successfully',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to change password',
                'data' => null
            ], 400);
        }
    }

    function changeEmail(Request $req)
    {
        $user = User::where('email', $req->current_email)->first();
        if (!$user) {
            return response([
                'message' => 'Current email not found',
                'data' => null
            ], 400);
        }

        // Check if the new email is already taken
        if (User::where('email', $req->new_email)->exists()) {
            return response([
                'message' => 'The new email is already taken',
                'data' => null
            ], 400);
        }

        // Generate a token
        $token = rand(100000, 999999);

        // Save it in your database
        DB::table('email_change_tokens')->updateOrInsert(
            ['email' => $req->current_email],
            [
                'new_email' => $req->new_email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $emailhelper = new MailController();
        $emailhelper->sendEmail($req->current_email, $token);

        return response([
            'message' => 'A token has been sent to your current email',
            'data' => null
        ], 200);
    }
    function verifyToken(Request $req)
    {
        $user = auth()->guard('api')->user();
        // Check if the token is correct
        $tokenData = DB::table('email_change_tokens')->where('email', $user->email)->first();
        if ($tokenData->token != $req->token) {
            return response([
                'message' => 'Token is incorrect',
                'data' => null
            ], 400);
        }

        // Update the email
        $user->email = $tokenData->new_email;
        $user->save();

        // Delete the token
        DB::table('email_change_tokens')->where('email', $user->email)->delete();

        return response([
            'message' => 'Email updated successfully',
            'data' => null
        ], 200);
    }

    function forgotPassword(Request $req)
    {
        $user = User::where('email', $req->email)->first();
        if (!$user) {
            return response([
                'message' => 'Email not found',
                'data' => null
            ], 400);
        }

        // Generate a token
        $token = rand(100000, 999999);

        // Save it in your database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $req->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $emailhelper = new MailController();
        $emailhelper->sendEmail($req->email, $token);

        return response([
            'message' => 'Token telah dikirimkan ke email',
            'data' => null
        ], 200);
    }

    function forgotPasswordVerifyToken(Request $req)
    {
        $tokenData = DB::table('password_reset_tokens')->where('email', $req->email)->first();


        if (!$tokenData) {
            return response([
                'message' => 'Email not found',
                'status' => 'error'
            ], 400);
        }

        if ($tokenData->token != $req->token) {
            return response([
                'message' => 'Token yang anda masukkan salah!',
                'status' => 'invalid'
            ], 400);
        }

        $tokenCreationTime = Carbon::parse($tokenData->created_at);
        $currentTime = Carbon::now();

        $tokenValidityPeriod = 5;

        if ($tokenCreationTime->diffInMinutes($currentTime) > $tokenValidityPeriod) {
            return response([
                'message' => 'Token yang anda masukkan telah kardaluarsa!',
                'status' => "expired"
            ], 400);
        }

        return response([
            'message' => 'Token yang anda masukkan benar.',
            'status' => 'valid'
        ], 200);
    }

    function resetPassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $reset = DB::table('password_reset_tokens')->where([
            ['token', '=', $req->token],
            ['email', '=', $req->email],
        ])->first();

        if (!$reset) {
            return response([
                'message' => 'Token yang anda masukkan salah!',
                'data' => null
            ], 400);
        }

        $user = User::where('email', $reset->email)->first();
        $user->password = Hash::make($req->password);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $req->token)->delete();

        return response([
            'message' => 'Password berhasil diubah',
            'data' => null
        ], 200);
    }

    function logout(Request $req)
    {
        Auth::logout();
        return redirect()->intended('dashboard');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser) {

                Auth::login($finduser);

                return redirect()->intended('dashboard');
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt('123456')
                ]);

                Auth::login($newUser);

                return redirect()->intended('dashboard');
            }

            // dd($user);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
