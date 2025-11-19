<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
    // public function login(Request $request)
    // {
    //     try{
    //         $data = $request->validate([
    //             'email' => ['required', 'email'] ,
    //             'password' => 'required'
    //         ]);
    //     } catch(ValidationException $e){
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $e->errors()
    //         ],422);
    //     }

    //     if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
    //         $request->session()->regenerate();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Đăng nhập thành công',
    //             'name' => Auth::user()->name,
    //             'email' => Auth::user()->email,
    //         ]);
    //     } else{
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Đăng nhập thất bại'
    //         ]);
    //     }
    //     // dd($request->all());
    //     // return response()->json($request->all());

    // }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
        // return response()->json(auth('api')->user());
    //     return response()->json([
    //     'success' => true,
    //     'user' => auth('api')->user(),
    //     'access_token' => $token,
    //     'token_type' => 'bearer',
    //     'name' => auth('api')->user()->name
    // ]);
    }


    public function register(Request $request)
{
    try{
        $data = $request->validate([
        'name' => ['required', 'min:3', 'max:20', Rule::unique('users', 'name')],
        'email' => ['required', 'email', Rule::unique('users', 'email')],
        'password' => ['required', 'min:3', 'confirmed'],
    ]);
    } catch(ValidationException $e){
        return response()->json([
            'success' => false,
            'errors' => $e->errors()  
        ], 422);
    }
    $data['password'] = Hash::make($data['password']);
    $user = User::create($data);
    

    return response()->json([
        'success' => true,
        'message' => 'Đăng ký thành công',
        'user'    => $user
    ], 201);
}


}
