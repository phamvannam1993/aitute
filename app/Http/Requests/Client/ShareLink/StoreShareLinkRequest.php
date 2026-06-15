<?php

namespace App\Http\Requests\Client\ShareLink;

use App\Enums\ShareLinkableTypeEnum;
use App\Helper\ApiErrorsMessageBag;
use App\Traits\ApiResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreShareLinkRequest extends FormRequest
{
    use  ApiResponses;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'share_linkable_id' => ['required'],
            'share_linkable_type' => [
                'required',
                function ($attribute, $value, $fail) {
                    $names = ShareLinkableTypeEnum::names();
                    if (!in_array($value, $names)) {
                        return $fail('Share linkable type không phù hợp');
                    }
                }
            ],
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
