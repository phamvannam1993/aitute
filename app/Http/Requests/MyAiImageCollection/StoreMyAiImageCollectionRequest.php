<?php

namespace App\Http\Requests\MyAiImageCollection;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponses;

class StoreMyAiImageCollectionRequest extends FormRequest
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
            'title' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
}
