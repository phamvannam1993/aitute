<?php

namespace App\Http\Requests\Client\AIImage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiResponses;
use App\Helper\ApiErrorsMessageBag;

class GenerateContentRequest extends FormRequest
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
            'prompt' => 'required|string'
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
