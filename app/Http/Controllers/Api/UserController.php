<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

use App\Models\Address;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
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
     * @param int $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UserRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $user = User::findOrFail($id);

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
     * Create function
     *
     * @param UserRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function create(UserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['type'] = 'client';
        $data['password'] = Hash::make($data['password']);

        $data['profile_picture'] = $request->file('profile_picture')->store('users', 'public');
        if (!$data['profile_picture']) return $this->error(["Image could not be stored!"]);

        try {
            $user = \DB::transaction(function () use ($data) {
                $address = Address::create($data);
                $data['address_id'] = $address->id;
                return User::create($data);
            });
        } catch (\Exception $e) {
            \Storage::disk('public')->delete($data['profile_picture']);
            throw new HttpResponseException($this->error([$e->getMessage()]));
        }

        return $this->success(["User created."], $user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        return $this->success(["User's infos."], $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->deleted_at) {
            $user->address()->delete();
            \Storage::disk('public')->delete($user->profile_picture);
            return $this->success(['User deleted definitively.']);
        }

        if ($user->type == "administrator" && count(User::where('type', 'administrator')->get())) {
            return $this->error(['The system require at least ONE administrator'], 405);
        }

        $user->delete();

        return $this->success(['User deleted.']);
    }

    /**
     * Returns all user's companies.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getCompanies(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $companyUsers['companies'] = $user->companies()->pluck('cnpj', 'id');
        return $this->success(["List of user's companies."], $companyUsers);
    }
}
