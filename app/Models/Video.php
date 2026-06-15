<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Video extends Model
{

    protected $table = 'videos';
    use HasFactory;

    protected $fillable = ['user_id', 'thumbnail', 'business_project_id', 'x_fade', 'debug', 'description', 'transcription_info', 'error_msg', 's3_url', 's3_url_video_audio', 's3_url_video_result', 's3_url_video_image', 'ratio', 'transcription_url', 'duration', 'is_transcription', 'image_url', 'audio_url'];
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function s3Url(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function xFades() {
        return  [
            [
                "key" => "fade",
                "value" =>"Ánh sáng mờ dần",
                 "isDisabled" => true
            ],
            [
                "key" => "fadeblack",
                "value" => "Chuyển dần sang màu đen",
                 "isDisabled" => true
            ],
            [
                "key" => "fadewhite",
                "value" => "Chuyển dần sang màu trắng",
                 "isDisabled" => true
            ],
            [
                "key" => "distance",
                "value" => "Chuyển dần theo khoảng cách",
                 "isDisabled" => true
            ],
            [
                "key" => "slideleft",
                "value" => "Trượt sang trái",
                "isDisabled" => false
            ],
            [
                "key" => "slideright",
                "value" => "Trượt sang phải",
                "isDisabled" => false
            ],
            [
                "key" => "slideup",
                "value" => "Trượt lên trên",
                "isDisabled" => false
            ],
            [
                "key" => "slidedown",
                "value" => "Trượt xuống dưới",
                "isDisabled" => false
            ],
             [
                "key" => "smoothleft",
                "value" => "Chuyển động mượt sang trái",
                 "isDisabled" => true
            ],
            [
                "key" => "smoothright",
                "value" => "Chuyển động mượt sang phải",
                "isDisabled" => true
            ],
            [
                "key" => "smoothup",
                "value" => "Chuyển động mượt lên trên",
                "isDisabled" => true
            ],
            [
                "key" => "smoothdown",
                "value" => "Chuyển động mượt xuống dưới",
                 "isDisabled" => true
            ],
            [
                "key" => "circlecrop",
                "value" => "Cắt hình tròn",
                "isDisabled" => false
            ], 
            [
                "key" => "rectcrop",
                "value" => "Cắt hình vuông",
                "isDisabled" => false
            ], 
             [
                "key" => "circleclose",
                "value" => "Vòng tròn đóng lại",
                "isDisabled" => false
            ], 
            [
                "key" => "circleopen",
                "value" => "Vòng tròn mở ra",
                "isDisabled" => false
            ], 
            [
                "key" => "horzclose",
                "value" => "Đóng theo chiều ngang",
                 "isDisabled" => true
            ], 
             [
                "key" => "horzopen",
                "value" => "Mở heo chiều ngang",
                 "isDisabled" => true
            ], 
             [
                "key" => "vertclose",
                "value" => "Đóng theo chiều dọc",
                 "isDisabled" => true
            ], 
            [
                "key" => "vertopen",
                "value" => "Mở theo chiều ngang",
                 "isDisabled" => true
            ], 
            [
                "key" => "diagbl",
                "value" => "Chặn chéo",
                 "isDisabled" => true
            ], 
            [
                "key" => "diagbr",
                "value" => "Ngắt chéo",
                 "isDisabled" => true
            ], 
            [
                "key" => "diagtl",
                "value" => "Nền chéo",
                 "isDisabled" => true
            ], 
            [
                "key" => "diagtr",
                "value" => "Chuyển đổi chéo",
                 "isDisabled" => true
            ], 
            [
                "key" => "hlslice",
                "value" => "Phân đoạn cao-thấp",
                 "isDisabled" => true
            ], 
             [
                "key" => "hrslice",
                "value" => "Phân đoạn ngang",
                 "isDisabled" => true
            ], 
            [
                "key" => "vuslice",
                "value" => "Phân đoạn dọc dưới lên trên",
                 "isDisabled" => true
            ], 
             [
                "key" => "vdslice",
                "value" => "Phân đoạn dọc trên xuống dưới",
                 "isDisabled" => true
            ], 
             [
                "key" => "dissolve",
                "value" => "Làm mờ dần",
                 "isDisabled" => true
            ], 
             [
                "key" => "pixelize",
                "value" => "Làm mờ dạng pixel",
                 "isDisabled" => true
            ], 
            [
                "key" => "radial",
                "value" => "Xung quanh một điểm",
                 "isDisabled" => true
            ], 
            [
                "key" => "hblur",
                "value" => "Làm mờ ngang",
                "isDisabled" => true
            ]
        ]; 
    }
}
