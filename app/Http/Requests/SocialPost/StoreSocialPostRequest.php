<?php

namespace App\Http\Requests\SocialPost;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialPostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required'],
            'files.*' => ['max:51200'],
            'files' => ['array'],
        ];
    }

    public function attributes()
    {
        return [
            'files.*' => 'files',
        ];
    }
}
