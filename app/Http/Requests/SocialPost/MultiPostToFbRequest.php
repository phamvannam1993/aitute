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
class MultiPostToFbRequest extends FormRequest
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
            'post_datas' => ['required', 'array'],
            'facebook_fanpage_id' => ['required'],
            'post_datas.*.content' => ['required', 'string'],
            'post_datas.*.files.*' => ['max:51200'],
            'post_datas.*.files' => ['array'],
            'post_datas.*.published' => ['required'],
            'post_datas.*.scheduled_publish_time' => [
                function ($attribute, $value, $fail) {
                    $keyPublished = str_replace(".scheduled_publish_time", ".published", $attribute);
                    $published = collect($this->all())->dot()[$keyPublished];
                    if ($published) {
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
            'post_datas.*.content' => 'content',
            'post_datas.*.files' => 'files',
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
