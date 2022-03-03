<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use App\Traits\ResponseApi;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\UserSearchParamsRequest;
use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;

class CompanyController extends Controller
{
    use ResponseApi;

    /**
     * Update function
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $companies = Company::all();
        return $this->success(["List of all companies."], $companies);
    }

    /**
     * Update function
     *
     * @param CompanyRequest $request
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function update(CompanyRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $company = Company::find($id);
        $company->update($data);

        $address = Address::find($company->address_id);
        $address->update($data);

        return $this->success(["Company updated."], $company);
    }

    /**
     * Create function
     *
     * @param CompanyRequest $request
     * @return JsonResponse
     */
    public function create(CompanyRequest $request): JsonResponse
    {
        $data = $request->validated();

        $address = Address::create($data);
        $data['address_id'] = $address->id;

        $company = Company::create($data);

        return $this->success(["Company created."], $company);
    }

    /**
     * Display the specified resource.
     *
     * @param  UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $company = Company::find($id);
        return $this->success(["Company's infos."], $company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $company = Company::find($id);
        $company->delete();
        return $this->success(["Company deleted."], $company);
    }

    /**
     * Returns all company's users.
     *
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function get_users($id): JsonResponse
    {
        $company = Company::find($id);
        $companyUsers['company'] = [
            "id" => $company->id,
            "name" => $company->name,
            "cnpj" => $company->cnpj,
            "users" => $company->users->pluck('name', 'id')
        ];
        return $this->success(["List of company's users."], $companyUsers);
    }

    /**
     * Adds users to the company.
     *
     * @param UserSearchParamsRequest $request
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function add_users(UserSearchParamsRequest $request, $id): JsonResponse
    {
        $data = $request->safe()->only('users')['users'];

        $company = Company::find($id);
        if (!$company) {
            return $this->error(["Company not found."], 404);
        }

        $newUsers = User::whereIn('id', isset($data['id']) ? $data['id'] : [])
            ->orWhereIn('cpf', isset($data['cpf']) ? $data['cpf'] : [])
            ->orWhereIn('email', isset($data['email']) ? $data['email'] : [])
            ->pluck('email', 'id')
            ->toArray();

        $existingUsers = $company->users->pluck('email', 'id')->toArray();

        $addedUsers = array_unique(array_diff($newUsers, $existingUsers));
        if (empty($addedUsers)) {
            return $this->error(["No users to add."], 404);
        }

        $ids = array_keys($addedUsers);
        $company->users()->attach($ids);

        return $this->success(["Users added to the company."], $addedUsers);
    }

    /**
     * Adds users to the company.
     *
     * @param UserSearchParamsRequest $request
     * @param UnsignedBigInteger $id
     * @return JsonResponse
     */
    public function remove_users(UserSearchParamsRequest $request, $id): JsonResponse
    {
        $data = $request->safe()->only('users')['users'];

        $company = Company::find($id);
        if (!$company) {
            return $this->error(["Company not found."], 404);
        }

        $removingUsers = User::whereIn('id', isset($data['id']) ? $data['id'] : [])
            ->orWhereIn('cpf', isset($data['cpf']) ? $data['cpf'] : [])
            ->orWhereIn('email', isset($data['email']) ? $data['email'] : [])
            ->pluck('email', 'id')
            ->toArray();

        $existingUsers = $company->users->pluck('email', 'id')->toArray();

        $removedUsers = array_unique(array_intersect($removingUsers, $existingUsers));
        if (empty($removedUsers)) {
            return $this->error(["No users to remove."], 404);
        }

        $ids = array_keys($removedUsers);
        $company->users()->detach($ids);

        return $this->success(["Users removed from the company."], $removedUsers);
    }
}
