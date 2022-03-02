<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Traits\ResponseApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

use App\Models\Address;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    use ResponseApi;

    /**
     * Update function
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = User::all();
        return $this->success(["List of all users."], $user);
    }

    /**
     * Update function
     *
     * @param UserRequest $request
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function update(UserRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $user = User::find($id);
        $data['password'] = Hash::make($data['password']);
        $user->update($data);

        $address = Address::find($user->address_id);
        $address->update($data);

        return $this->success(["User updated."], $user);
    }

    /**
     * Create function
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function create(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $address = Address::create($data);
        $data['address_id'] = $address->id;
        $data['type'] = 'client';
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->success(["User created."], $user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);
        return $this->success(["User's infos."], $user);
    }
}
