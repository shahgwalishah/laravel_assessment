<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /*
     * register and send 6 digit code to user email address *
     * */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'user_name' => 'required|min:4|max:20',
            'avatar'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:max_width=256,max_height=256'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        try {
            $avatar = '';
            if($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = $request->file('avatar')->getClientOriginalName();
                $path = 'uploads';
                $avatar = $path . '/' . $filename;
                $file->move($path,$avatar);
            }
            $code = $this->generateRandomString(6);;
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_name' => $request->user_name,
                'password' => bcrypt($request->password),
                'avatar' => $avatar,
                'pin_code' => $code,
                'user_role' => 'user',
                'registered_at' => Carbon::now()
            ]);
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            if (auth()->attempt($credentials)) {
                $token = auth()->user()->createToken('android')->accessToken;
                $sender_email = 'walimstr218@gmail.com';
                Mail::send('sixdigitCode', ['code' => $code], function ($message) use ($request, $sender_email) {
                    $message->from($sender_email);
                    $message->to($request->email)->subject("six digit code");
                });
                return collect([
                    'status' => true,
                    'message' => 'we will send you a 6 digit code in you email address',
                    'data' => auth()->user(),
                    'token' => $token['token']
                ]);
            } else {
                return response()->json(['error' => 'Something went wrong'], 401);
            }
        } catch (\Exception $e) {
            return collect([
               'status' => false,
               'message' => $e->getMessage()
            ]);
        }
    }

    /*
     * generate random string generator
     * */
    public function generateRandomString($length = 25) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /*
     * verify email using 6 digit code
     * */
    public function verifyEmail(Request $request) {
        if($request->code) {
            $check = User::where('pin_code','=',$request->code)->first();
            if(!$check) {
                return collect([
                    'status' => false,
                    'message' => 'pin code not matched ... !'
                ]);
            }
            User::where('pin_code','=',$request->code)->update([
               'email_verified' => true
            ]);
            return collect([
               'status' => true,
               'message' => 'email verified successfully ... !'
            ]);
        }
    }

    /*
     * login with user credentials
     * */
    public function login(Request $request) {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        $user = User::where('email','=',$request->email)->first();
        if(!$user->email_verified) {
            return response()->json(['status' => false , 'message' => 'please verify your email address first']);
        }
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('android')->accessToken;
            return response()->json(['user' => auth()->user() , 'token' => $token['token']], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    /*
     * update user profile
     * */
    public function updateProfile(Request $request) {
        $user = auth()->user();
        dd($user);
        try {
            if($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = $request->file('avatar')->getClientOriginalName();
                $path = 'uploads';
                $avatar = $path . '/' . $filename;
                $file->move($path,$avatar);
                User::where('id','=',$user->id)->update([
                    'avatar' => $avatar
                ]);
            }
            if($request->password) {
                User::where('id','=',$user->id)->update([
                    'password' => bcrypt($request->password)
                ]);
            }
            if($request->email) {
                User::where('id','=',$user->id)->update([
                    'email' => $request->email
                ]);
            }
            User::where('id','=',$user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'user_name' => $request->user_name,
                'user_role' => 'user',
                'registered_at' => Carbon::now()
            ]);
            return collect([
                'status' => true,
                'message' => 'profile update successfully ... !',
                'data' => Auth::user()
            ]);
        } catch (\Exception $e) {
            return collect([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}
