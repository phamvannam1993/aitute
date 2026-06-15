<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Music extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'music';

    /**
     * Các thuộc tính có thể được gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'sample_audio',
        'result_audio',
        'prompt',
        'lyrics',
        'user_id',
        'model',
        'image_url',
    ];

    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'share_linkable');
    }
}
