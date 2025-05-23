<?php

namespace App\Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
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
            'login_type' => 'required|in:google,facebook,email',
            'email' => 'required_if:login_type,email',
            'google_id' => 'required_if:login_type,google',
            'facebook_id' => 'required_if:login_type,facebook',
            'password' => 'required_if:login_type,email',
        ];
    }
}
