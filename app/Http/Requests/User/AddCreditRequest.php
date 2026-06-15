<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponses;
use Illuminate\Validation\Rule;

class AddCreditRequest extends FormRequest
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
            'user_id' => 'required|integer',
            'credit_add' => [
                'nullable',
                Rule::requiredIf(fn() => !$this->get('vip_expired_at_add'))
            ],
            'vip_expired_at_add' => [
                'nullable',
                Rule::requiredIf(fn() => !$this->get('credit_add'))
            ],
        ];
    }
}
