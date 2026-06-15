<?php

namespace App\Http\Requests\SocialPost;

use App\Constants\Facebook;
use App\Helper\ApiErrorsMessageBag;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobCreateContentAIRequest extends FormRequest
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
            'social_postable_id' => ['required'],
            'social_postable_type' => ['required'],
            'description' => ['required', 'string'],
            'category' => ['nullable'],
            'project_name' => ['nullable'],
            'target' => ['nullable'],
            'options_rewrite' => ['nullable'],
            'limit' => ['required', 'int', 'in:100,200,500,1000,2000,3000'],
            'lang' => ['required', 'string'],
            'date_range' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    list($startDate, $endDate) = $value;
                    $endDate = new Carbon($endDate);
                    $maxSchedule = now()->addDays(Facebook::DAYS_MAX_SCHEDULE_POST);
                    if ($maxSchedule->lessThan($endDate)) {
                        return $fail('Lịch bài viết không vượt quá ' . Facebook::DAYS_MAX_SCHEDULE_POST . ' ngày từ thời điểm tạo bài viết.');
                    }
                },
                function ($attribute, $value, $fail) {
                    list($startDate, $endDate) = $value;
                    $startDate = new Carbon($startDate);
                    $endDate = new Carbon($endDate);
                    $limit = $startDate->diffInDays($endDate);
                    if ($limit > Facebook::DAYS_MAX_SCHEDULE_POST) {
                        return $fail('Khoảng cách ngày đặt lịch không quá ' . Facebook::DAYS_MAX_SCHEDULE_POST . ' ngày.');
                    }
                }
            ],
            'date_range.*' => ['string'],
            'time' => ['required', 'array'],
        ];
    }

    public function attributes()
    {
        return [
            'date_range' => 'ngày đăng',
            'time' => 'giờ đăng',
            'time.hours' => 'giờ',
            'time.minutes' => 'phút',
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