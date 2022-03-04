<?php

namespace App\Http\Requests;

use App\Traits\AddressValidation;
use App\Traits\ResponseApi;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompanyRequest extends FormRequest
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
        $uniqueRule = 'unique:companies';
        if ($this->route('id'))
            $uniqueRule .= ',NULL,' . $this->route('id');

        $companyRules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'cnpj' => ['required', 'string', 'digits:14', $uniqueRule]
        ];

        $rules = array_merge($this->addressRules(), $companyRules);

        return $rules;
    }
}
