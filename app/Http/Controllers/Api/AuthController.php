<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails())
        {

            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $user = User::create($input);


        $success = [
            'token' => $user->createToken('DigiMarket')->accessToken,
            'name' => $user->name,
        ];

        event(new Registered($user)); // Trigger email verification

        return $this->sendResponse($success, 'User registered successfully');
    }

    public function signin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();

            // Create token
            $token = $user->createToken('DigiMarket')->accessToken;

            $success = [
                'token' => $token,
                'name' => $user->name,
            ];

            return $this->sendResponse($success, 'User login successfully');
        } else {
            return $this->sendError('Unauthorised', ['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
