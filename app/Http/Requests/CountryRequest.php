<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'country_name' => ['required','string','max:255',Rule::unique('countries')->ignore($this->country)],
            'capital_city' => ['required', 'string', 'max:255', Rule::unique('countries')->ignore($this->country)],
        ];
    }
    public function messages()
    {
        return [
            'country_name.required' => 'Country name is required',
            'capital_city.required' => 'Capital city is required',
            'country_name.unique' => 'Country name already exists',
            'capital_city.unique' => 'Capital city already exists',
        ];
    }
}
