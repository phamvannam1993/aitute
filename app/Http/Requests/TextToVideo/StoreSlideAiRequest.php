<?php

namespace App\Http\Requests\TextToVideo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiResponses;
use App\Helper\ApiErrorsMessageBag;

class StoreSlideAiRequest extends FormRequest
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
            'topic' => 'required_without:file_name|nullable|string',
            'file_name' => 'required_without:topic|nullable|string',
            'slide_contents' => 'required|array',
            'slide_contents.*.title' => 'required|string',
            'slide_contents.*.descriptions' => 'required|array',
            'slide_contents.*.keyword' => 'required|string',
            'slide_contents.*.voice_type' => 'string',
            'slide_contents.*.virtual' => 'nullable|array',
            'slide_contents.*.background' => 'string',
            'theme.id' => 'integer',
            'theme.name' => 'string',
            'theme.textColor' => 'string',
            'theme.headingColor' => 'string',
            'theme.backgroundColor' => 'string',
            'theme.slideColor' => 'string',
            'video_url'=> 'string',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            $apiErrors = new ApiErrorsMessageBag($errors);
            throw new HttpResponseException($this->unprocessableResponse($apiErrors));
        }

        throw new ValidationException($validator);
    }
}
