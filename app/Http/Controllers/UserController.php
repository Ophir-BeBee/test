<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\UserRegisterRequest;

class UserController extends Controller
{

    protected $user;

    public function __construct(User $model)
    {
        $this->user = $model;
    }

    //register
    public function register(UserRegisterRequest $request){
        $data = $this->changeRegisterFormToArray($request);
        $userData = $this->user->create($data);

        $token = $userData->createToken('myapptoken')->plainTextToken;

        $responseData = [
            'userData' => $userData,
            'token' => $token
        ];
        return response()->json($responseData,200);
    }

    //login
    public function login(UserLoginRequest $request){
        $userData = $this->user->where('email',$request->email)->first();

        if($userData && Hash::check($request->password,$userData->password)){

            $token = $userData->createToken('mapptoken')->plainTextToken;
            $responseData = [
                'userData' => $userData,
                'token' => $token
            ];

            return response()->json($responseData, 200);
        }


            return response([
                'message' => 'Email not found'
            ],404);

    }

    //logout
    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response()->json([
            "message" => "Logged out successfully"
        ]);
    }

    //change register form to array
    private function changeRegisterFormToArray($request){
        return [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];
    }

}
