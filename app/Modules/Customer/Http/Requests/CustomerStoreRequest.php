<?php

namespace App\Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerStoreRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required', 
                'email',
                Rule::unique('customers')
                    ->where(fn ($query) => $query->where('company_id', app('company')->company()->id))
            ],
            'password' => 'required',
            'type' => 'required|in:person,company',
            'document' => 'required',
            'birthdate' => 'date|required',
        ];
    }
}
