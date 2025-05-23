<?php

namespace App\Modules\Company\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            'name' => 'sometimes',
            'domain' => 'sometimes',
            'document' => 'sometimes',
            'contact_email' => 'sometimes|email',
            'settings' => 'array|sometimes',
            'settings.*.key' => 'required',
            'settings.*.type' => 'required',
            'settings.*.value' => 'required',
            'settings.*.delete' => 'sometimes|boolean',
            'settings.*.id' => 'sometimes',
        ];
    }
}
