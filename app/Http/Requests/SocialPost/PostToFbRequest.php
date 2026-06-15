<?php

namespace App\Http\Requests\SocialPost;

use App\Constants\Facebook;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostToFbRequest extends FormRequest
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
            'facebook_fanpage_id' => ['required'],
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
            'files.*' => 'files',
        ];
    }
}
