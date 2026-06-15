<?php

namespace App\Http\Requests\SocialPost;

use App\Constants\Facebook;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateToFbRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
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
                    $scheduled_publish_time = new Carbon($value);

                    if ($minSchedule->greaterThan($scheduled_publish_time)) {
                        return $fail('Lịch bài viết phải sau 20 phút và trước 29 ngày từ thời điểm tạo bài viết.');
                    }
                    if ($maxSchedule->lessThan($scheduled_publish_time)) {
                        return $fail('Lịch bài viết phải sau 20 phút và trước 29 ngày từ thời điểm tạo bài viết.');
                    }
                }
            ],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'tiêu đề',
            'content' => 'nội dung',
            'files.*' => 'files',
            'scheduled_publish_time' => 'ngày đăng',
        ];
    }
}
