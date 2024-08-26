<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email'  => 'required',
            'password'  => 'required',
            'confirm_password'  => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ada Kesalahan',
                'data' => $validator->errors(),
            ]);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Daftar',
            'data' => $success
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $success['token'] = $auth->createToken('auth_token')->plainTextToken;
            $success['name'] = $auth->name;

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Masuk',
                'data' => $success
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password tidak valid',
                'data' => null
            ]);
        }
    }

    public function logout(Request $request){
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
        }

       return response()->json([
         'message' => 'Berhasil Logout',
       ]);
    }
}
