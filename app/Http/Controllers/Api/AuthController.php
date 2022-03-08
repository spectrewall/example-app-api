<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Traits\ResponseApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\Address;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login function
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::query()->where("email", $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error(['Invalid email or password.'], 401);
        }

        return $this->success('User logged in.', ['token' => $user->createToken('auth')->plainTextToken]);
    }
}
