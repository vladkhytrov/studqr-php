<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::query()->create([
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'user_type' => 'student',
            'password'  => bcrypt($request->input('password')),
        ]);

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response()->json($response, 201);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response()->json($response);
    }

}
