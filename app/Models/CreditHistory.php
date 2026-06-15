<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditHistory extends Model
{
    protected $table = 'credit_histories';

    protected $fillable = [
        'user_id',
        'credit',
        'screen_id',
        'feature_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function listFeature() {
        return [
            1 => 'Tạo Gợi ý nội dung',
            2 => 'Tạo Ảnh',
            3 => 'Tạo Phim',
            4 => 'Tạo Video',
            5 => 'Tạo MC ảo',
            6 => 'Tạo Nội dung cho sản phẩm',
            7 => 'Nội dung thành hình ảnh',
            8 => 'Hình ảnh thành video',
            9 => 'Tạo âm nhạc từ văn bản',
            10 => 'Tạo bài hát từ văn bản',
            11 => 'Chatbot',
            12 => 'Tạo Âm Thanh',
            13 => 'Tạo trợ lý ảo',
            14 => 'Giọng nói thành văn bản',
            15 => 'Sử dụng trợ lý ảo + tên trợ lý',
            16 => 'Lồng giọng',
            17 => 'Tạo video theo template',
            18 => 'Tạo nội dung Mô tả video theo template'  ,
            19 => 'Tạo văn bản thành giọng nói',
            20 => 'Tạo ảnh từ ảnh',
            21 => 'Tạo ảnh phối cảnh',
            22 => 'Tạo banner- poster',
            23 => 'Tạo nội dung video từ link',
            24 => 'Tạo video từ link',
            25 => 'Làm đẹp ảnh',
            26 => 'Tạo phụ đề',
            27 => 'Tạo văn bản thành giọng nói (chức năng MC ảo)',
            28 => 'Tạo video phân cảnh',
            29 => 'Tạo nội dung phân cảnh',
            30 => 'Tạo nội dung phân tích ',
            31 => 'Tạo chủ đề các bài viết hẹn trước đăng facebook',
            32 => 'Tạo nội dung bài viết theo chủ đề đăng facebook',
            33 => 'Tạo prompt cho hình ảnh theo chủ đề đăng facebook',
            34 => 'Tạo hình ảnh theo prompt đăng facebook',
            35 => 'Thần số học',
            36 => 'Chatbot fanpage',
            37 => 'Ghép ảnh',
            38 => 'Tạo ảnh với người mẫu',
            39 => 'Tạo nội dung facebook',
            40 => 'Làm nét ảnh',
            41 => 'Tự động tạo video',
            42 => 'Tạo video từ ảnh',
        ];
    }

    public static function listScreen() {
        return [
            1 => 'Trang chủ',
            2 => 'Tạo Ảnh',
            3 => 'Tạo Phim',
            4 => 'Tạo Video',
            5 => 'Tạo MC Ảo',
            6 => 'Tạo Nội dung cho sản phẩm',
            7 => 'Nội dung thành hình ảnh',
            8 => 'Hình ảnh thành video',
            9 => 'Tạo âm nhạc từ văn bản',
            10 => 'Tạo bài hát từ văn bản',
            11 => 'Chatbot',
            12 => 'Âm Thanh',
            13 => 'Trợ lý ảo',
            14 => 'ChatBot',
            15 => 'Tạo từng step',
            16 => 'Tôi là A.I',
            17 => 'Tạo video theo template ',
            18 => 'Tạo nội dung Mô tả video theo template',
            19 => 'Tạo văn bản thành giọng nói',
            20 => 'Tạo ảnh từ ảnh',
            21 => 'Hình phối cảnh',
            22 => 'Tạo banner- poster',
            23 => 'Tạo video từ link',
            24 => 'Làm đẹp ảnh',
            25 => 'Chi tiết phim',
            30 => 'A.I Truyền Thông',
            31 => 'Quản lý social',
            32 => 'Tạo nội dung bài viết theo chủ đề đăng facebook',
            33 => 'Tạo prompt cho hình ảnh theo chủ đề đăng facebook',
            34 => 'Tạo hình ảnh theo prompt đăng facebook',
            35 => 'Thần số học',
            36 => 'Chatbot fanpage',
            37 => 'AI Bán Hàng',
            38 => 'Giải pháp bán hàng chuyên sâu',
        ];
    }
}
