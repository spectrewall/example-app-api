<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseApi;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = auth()->user();
        return $this->success(['Your infos.'], $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function update(UserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::find(auth()->id());

        $data['password'] = Hash::make($data['password']);
        $user->update($data);

        $address = Address::find($user->address_id);
        $address->update($data);

        return $this->success(['Profile updated.'], $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        $user = User::find(auth()->id());

        if ($user->type == "administrator" && count(User::where('type', 'administrator')->get())) {
            return $this->error(['The system require at least ONE administrator'], 405);
        }

        $user->delete();
        return $this->success(['Profile deleted.'], $user);
    }

    /**
     * Returns all user's companies.
     *
     * @return JsonResponse
     */
    public function getCompanies(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $companyUsers['companies'] = $user->companies()->pluck('cnpj', 'id');
        return $this->success(["Your companies."], $companyUsers);
    }
}
