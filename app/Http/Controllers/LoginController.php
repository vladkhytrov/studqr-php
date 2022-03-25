<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    public function register(Request $request)
    {
//        $request->validate([
//            'name' => 'required|string',
//            //'email' => 'required|string|unique:users, email',
//            'password' => 'required|string'
//        ]);

//        if ($validator->fails()) {
//            return response(['error' => $validator->errors()]);
//        }

        $user = User::create([

            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'user_type' => 'student',
            'password' => bcrypt($request->input('password')),
        ]);
        $user->save();

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('user_token')->plainTextToken;
    }
}
