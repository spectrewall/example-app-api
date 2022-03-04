<?php

namespace App\Http\Requests;

use App\Traits\AddressValidation;
use App\Traits\ResponseApi;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    use ResponseApi, AddressValidation;

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
        $uniqueRule = 'unique:users';
        if ($this->isMethod('PUT')) {
            $uniqueRule .= ',NULL,';

            if ($this->route('id')) $uniqueRule .= $this->route('id');
            else $uniqueRule .= auth()->id();
        }

        $userRules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', $uniqueRule],
            'cpf' => ['required', 'string', 'digits:11', $uniqueRule],
            'password' =>  [
                'required', 'string', 'min:8', 'max:32', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/', 'confirmed'
            ]
        ];

        $rules = array_merge($this->addressRules(), $userRules);

        return $rules;
    }
}
