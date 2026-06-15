<?php

namespace App\Http\Requests\FacebookFanpage;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacebookFanpageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'facebook_id' => ['required'],
            'fanpages' => ['required', 'array'],
        ];
    }
}
