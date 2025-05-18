<?php

namespace App\Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Ajuste conforme sua regra de autorização
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'street' => 'sometimes|required|string|max:255',
            'number' => 'sometimes|required|string|max:50',
            'complement' => 'nullable|string|max:255',
            'district' => 'sometimes|required|string|max:255',
            'postcode' => 'sometimes|required|string|max:20',
            'city' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',
        ];
    }
}
