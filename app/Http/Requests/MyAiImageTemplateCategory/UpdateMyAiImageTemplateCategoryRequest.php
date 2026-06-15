<?php

namespace App\Http\Requests\MyAiImageTemplateCategory;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponses;

class UpdateMyAiImageTemplateCategoryRequest  extends FormRequest
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
            'id' => 'required|integer',
            'my_ai_image_collection_id' => 'required|integer|exists:my_ai_image_collections,id',
            'title' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
        ];
    }
}
