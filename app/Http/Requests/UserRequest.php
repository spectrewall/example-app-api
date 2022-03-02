<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\ResponseApi;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $addressRequest = new AddressRequest();
        $addressRules = $addressRequest->rules();

        $uniqueRule = null;
        if ($this->getRequestUri() == "/api/profile/update") {
            $uniqueRule = Rule::unique((new User)->getTable())->ignore(auth()->id());
        } elseif ($this->getRequestUri() == "/api/signup") {
            $uniqueRule = 'unique:users';
        } else {
            $id = explode('/', $this->getRequestUri())[3];
            $uniqueRule = Rule::unique((new User)->getTable())->ignore($id);
        }

        $userRules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', $uniqueRule],
            'cpf' => ['required', 'string', 'digits:11', $uniqueRule],
            'password' =>  [
                'required', 'string', 'min:8', 'max:32', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/', 'confirmed'
            ]
        ];

        $rules = Arr::collapse([$addressRules, $userRules]);

        return $rules;
    }
}
