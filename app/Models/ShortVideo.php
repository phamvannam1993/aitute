<?php

namespace App\Models;

use App\Helper\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ShortVideo extends Model
{

    protected $table = 'short_videos';
    use HasFactory;

    protected $fillable = ['user_id', 'video_id', 'position_camera', 'image_description', 'scene_number', 'task_id', 'thumbnail', 'description', 'audio_description', 's3_url', 'transcription_url', 'model', 'voice_over', 'image_url', 'audio_url'];
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }

    protected function s3Url(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Helpers::preSignedS3Url($value),
        );
    }

    protected function positionCamera() { 
        return  [
            [
                "key" => 1,
                "value" =>"Máy quay ngang từ trái sang phải",
                "des" => "The camera pans smoothly from left to right. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 2,
                "value" =>"Máy quay ngang từ phải sang trái",
                "des" => "The camera pans smoothly from right to left. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 3,
                "value" =>"Máy quay nghiêng lên",
                "des" => "The camera tilts upward, slowly revealing the upper portion of the scene and drawing attention to the height or the sky. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 4,
                "value" =>"Máy quay nghiêng xuống",
                "des" => "The camera tilts downward, gradually revealing the lower portion of the scene and focusing on elements below. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 5,
                "value" =>"Máy quay phóng to",
                "des" => "The camera zooms in, gradually focusing on a specific subject or detail within the frame, drawing the viewer's attention closer. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 6,
                "value" =>"Máy quay thu nhỏ",
                "des" => "The camera zooms out, gradually revealing a wider view of the scene and providing more context or background details. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 7,
                "value" =>"Máy quay nhìn thẳng từ trên xuống",
                "des" => "The camera captures a top-down view, looking directly downward to showcase the scene from above. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 8,
                "value" =>"Máy quay di chuyển",
                "des" => "The camera moves through the scene, creating dynamic motion and following the subject or exploring the environment. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 9,
                "value" =>"Máy quay đặt gần",
                "des" => "The camera is placed in close proximity to the subject, capturing detailed and intimate shots. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
            [
                "key" => 10,
                "value" =>"Máy quay đặt ở khoảng cách xa",
                "des" => "The camera is positioned far away. The motion is fluid and takes 5 seconds",
                "isDisabled" => false
            ],
        ];
    }

    protected function positionCameraByKey($key, $positionCameras) {  
        foreach($positionCameras as $pCamera) {
            if($pCamera["key"] == $key) {
                return $pCamera["des"];
            }
        }
        return "";
    }
}
