<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function home(){
        return view('home');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email'  => 'required',
            'password'  => 'required',
            'confirm_password'  => 'required|same:password',
        ]);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Ada Kesalahan',
            //     'data' => $validator->errors(),
            // ]);
        // }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil Daftar',
        //     'data' => $success
        // ]);

        // return view('auth.register');
        return redirect('home');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $success['token'] = $auth->createToken('auth_token')->plainTextToken;
            $success['name'] = $auth->name;

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Berhasil Masuk',
            //     'data' => $success
            // ]);
            return redirect('home');
        }
        // else{
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Email atau Password tidak valid',
        //         'data' => null
        //     ]);
        // }
        
    }

    public function logout(Request $request){
        $user = $request->user();
        if ($user) {
            $currentAccessToken = $user->currentAccessToken();
            if ($currentAccessToken) {
                // Menghapus token akses saat ini
                $currentAccessToken->delete();
            }            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('login');
        }

    //    return response()->json([
    //      'message' => 'Berhasil Logout',
    //    ]);

        return redirect('login')->with('error', 'No user found to log out');
    }
}   
