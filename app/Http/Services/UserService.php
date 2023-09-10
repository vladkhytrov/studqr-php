<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'role'       => 'required|in:student,teacher',
            'email'      => 'required|unique:users,email',
            'password'   => 'required|string',
        ]);

        $user = User::query()->create([
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            'role'       => $request->input('role'),
            'email'      => $request->input('email'),
            'password'   => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'token' => $token,
            'user'  => $user->toArray(),
        ];

        return response()->json($response, 201);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->failed()],
                401
            );
        }

        $user = User::query()->where('email', $request->input('email'))->first();
        if (!$user) {
            return response()->json(
                ['errors' => ['email' => 'The provided credentials are incorrect: email']],
                401
            );
//            throw ValidationException::withMessages([
//                'email' => ['The provided credentials are incorrect. email'],
//            ]);
        }
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json(
                ['errors' => ['password' => 'The provided credentials are incorrect: password']],
                401
            );
//            throw ValidationException::withMessages([
//                'password' => ['The provided credentials are incorrect. pass'],
//            ]);
        }

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'token' => $token,
            'user'  => $user->toArray(),
        ];

        return response()->json($response);
    }

}
