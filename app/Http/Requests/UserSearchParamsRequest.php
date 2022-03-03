<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\ResponseApi;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserSearchParamsRequest extends FormRequest
{
    use ResponseApi;

    /**
     * Overriding response of failed validation
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->error($validator->errors()->all(), 422));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users' => ['required', 'array'],
            'users.id' => ['array'],
            'users.id.*' => ['numeric'],
            'users.cpf' => ['array'],
            'users.cpf.*' => ['digits:11'],
            'users.email' => ['array'],
            'users.email.*' => ['email']
        ];
    }
}
