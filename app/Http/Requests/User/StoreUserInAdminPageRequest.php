<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponses;

class StoreUserInAdminPageRequest extends FormRequest
{
    use  ApiResponses;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            'name' => 'required|between:3,30',
            'password' => 'required|string|between:6,50',
            'email' => [
                'email',
                'between:6,50',
                'unique:users',
            ],
            'config_aff_id' => [
                'required',
            ],
            'phone' => [
                'nullable',
                'regex:/^0[0-9]{9}$/'
            ],
            'isSendMail' => [
                'nullable',
            ],
            'is_gift' => [
                'nullable',
            ],
        ];
    }
}
