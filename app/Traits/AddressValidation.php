<?php

namespace App\Traits;

trait AddressValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function addressRules(): array
    {
        $basicNameRule = ['required', 'string', 'min:3', 'max:255'];

        return [
            'cep' => ['required', 'string', 'digits:8'],
            'street' => $basicNameRule,
            'neighborhood' => $basicNameRule,
            'city' => $basicNameRule,
            'state' => $basicNameRule,
        ];
    }
}
