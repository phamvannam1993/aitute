<?php

namespace App\Http\Requests\MyAiImageTemplateCategory;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponses;

class StoreMyAiImageTemplateCategoryRequest extends FormRequest
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
            'my_ai_image_collection_id' => 'required|integer|exists:my_ai_image_collections,id',
            'title' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
        ];
    }
}
