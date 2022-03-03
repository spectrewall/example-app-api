<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Traits\ResponseApi;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $addressRequest = new AddressRequest();
        $addressRules = $addressRequest->rules();

        $id = explode('/', $this->getRequestUri())[3];
        $uniqueRule = Rule::unique((new Company)->getTable())->ignore($id);

        $companyRules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'cnpj' => ['required', 'string', 'digits:14', $uniqueRule]
        ];

        $rules = Arr::collapse([$addressRules, $companyRules]);

        return $rules;
    }
}
