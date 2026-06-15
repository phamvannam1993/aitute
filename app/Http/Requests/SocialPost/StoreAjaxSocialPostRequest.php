<?php

namespace App\Http\Requests\SocialPost;

use App\Constants\Facebook;
use App\Helper\ApiErrorsMessageBag;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreAjaxSocialPostRequest extends FormRequest
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
            'title' => ['nullable'],
            'content' => ['required'],
            'social_postable_id' => ['required'],
            'social_postable_type' => ['required'],
            'published' => ['required'],
            'files.*' => ['max:51200'],
            'files' => ['array'],
            'scheduled_publish_time' => [
                Rule::requiredIf(fn() => !$this->get('published')),
                function ($attribute, $value, $fail) {
                    if ($this->get('published')) {
                        return true;
                    }

                    $minSchedule = now()->addMinutes(Facebook::MINUTES_MIN_SCHEDULE_POST);
                    $maxSchedule = now()->addDays(Facebook::DAYS_MAX_SCHEDULE_POST);
                    $scheduledPublishTime = new Carbon($value);

                    if ($minSchedule->greaterThan($scheduledPublishTime)) {
                        return $fail('Lịch bài viết phải sau ' . Facebook::MINUTES_MIN_SCHEDULE_POST . ' phút và trước ' . Facebook::DAYS_MAX_SCHEDULE_POST . ' ngày từ thời điểm tạo bài viết.');
                    }
                    if ($maxSchedule->lessThan($scheduledPublishTime)) {
                        return $fail('Lịch bài viết phải sau ' . Facebook::MINUTES_MIN_SCHEDULE_POST . ' phút và trước ' . Facebook::DAYS_MAX_SCHEDULE_POST . ' ngày từ thời điểm tạo bài viết.');
                    }
                }
            ],

        ];
    }

    public function attributes()
    {
        return [
            'title' => 'tiêu đề',
            'social_postable_id' => 'nền tảng',
            'content' => 'nội dung',
            'files.*' => 'files',
            'scheduled_publish_time' => 'ngày đăng',
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
