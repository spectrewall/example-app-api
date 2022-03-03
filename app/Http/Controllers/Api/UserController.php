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
        $users = User::all();
        return $this->success(["List of all users."], $users);
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
     * @param  UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);
        return $this->success(["User's infos."], $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if ($user->type == "administrator" && count(User::where('type', 'administrator')->get())) {
            return $this->error(['The system require at least ONE administrator'], 405);
        }

        $user->delete();
        return $this->success(['User deleted.'], $user);
    }

    /**
     * Returns all user's companies.
     *
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function get_companies($id): JsonResponse
    {
        $user = User::find($id);
        $companyUsers['companies'] = $user->companies->pluck('cnpj', 'id');
        return $this->success(["List of user's companies."], $companyUsers);
    }
}
