<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
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
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::findOrFail(auth()->id());

        $data['password'] = Hash::make($data['password']);

        $oldImage = null;
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('users', 'public');
            if (!$data['profile_picture']) return $this->error(["Image could not be stored!"]);
            $oldImage = $user->profile_picture;
        }

        try {
            \DB::transaction(function () use ($data, $user) {
                $user->update($data);
                $user->address()->first()->update($data);
            });
        } catch (\Exception $e) {
            if ($data['profile_picture']) \Storage::disk('public')->delete($data['profile_picture']);
            throw new HttpResponseException($this->error([$e->getMessage()]));
        }

        if (isset($oldImage)) \Storage::disk('public')->delete($oldImage);

        return $this->success(["User updated."], $user);
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

        return $this->success(['User deleted.']);
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
