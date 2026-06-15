<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditPackagesSeederv2 extends Seeder
{
    /**
     *  php artisan db:seed --class=CreditPackagesSeederv2
     */
    public function run()
    {
        DB::table('credit_packages')->delete();

        $data = [
            [
                'name' => 'Xây dựng THCN',
                'duration' => '12 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;"> A. Các ứng dụng A.I hỗ trợ công việc </p> <ol style="padding-left: 20px; margin: 0; list-style-type: decimal; list-style-position: outside;"> <li>Tạo ảnh avatar thương hiệu cá nhân</li> <li>Chat bot (Tích hợp GPT + Claude)</li> <li>Các trợ lý A.I</li> </ol> </div>',
                'price' => 2500000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/xay-dung-thuong-hieu-ca-nhan.html?utm_source=',
                'credit' => 50000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Standard'
            ],
            [
                'name' => 'Xây dựng THCN',
                'duration' => '1 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;"> A. Các ứng dụng A.I hỗ trợ công việc </p> <ol style="padding-left: 20px; margin: 0; list-style-type: decimal; list-style-position: outside;"> <li>Tạo ảnh avatar thương hiệu cá nhân</li> <li>Chat bot (Tích hợp GPT + Claude)</li> <li>Các trợ lý A.I</li> </ol> </div>',
                'price' => 300000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/xay-dung-thuong-hieu-ca-nhan.html?utm_source=',
                'credit' => 50000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Standard'
            ],
            [
                'name' => 'A.I Bán Hàng',
                'duration' => '12 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <!-- A --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;"> A. Giải pháp A.I bán hàng </p> <ol style="padding-left: 20px; margin-top: 0; list-style-type: decimal; list-style-position: outside;"> <li>Thiết lập thông tin sản phẩm</li> <li>Phân tích chuyên sâu về sản phẩm</li> <li>Tạo status quảng cáo</li> <li>Tạo chiến dịch quảng cáo</li> <li><span style="text-decoration: line-through; color: #999;">Tạo thơ quảng cáo sản phẩm</span></li> <li><span style="text-decoration: line-through; color: #999;">Tạo nhạc quảng cáo sản phẩm</span></li> <li><span style="text-decoration: line-through; color: #999;">Tạo status fanpage 1 tháng</span></li> <li>Bổ sung thông tin nâng cao</li> <li>Phân tích chuyên sâu về doanh nghiệp</li> <li>Chiến lược nội dung ngắn hạn</li> <li>Xem kết quả và hiệu chỉnh: Nội dung, Mục tiêu, Phong cách, Cảm xúc, định dạng cho bài quảng cáo</li> <li>Tạo ảnh quảng cáo sản phẩm (Tự động tạo ảnh, Tải ảnh, Tạo ảnh theo yêu cầu)</li> <li>Tạo Video quảng cáo sản phẩm (Tích hợp tự động tạo Video, Tải Video, Tạo Video theo yêu cầu)</li> <li>Kết nối Fanpage và đặt lịch đăng</li> <li><span style="text-decoration: line-through; color: #999;">A.I AGENT tự động sản xuất nội dung, ảnh, Video và đăng bài fanpage cả tháng, năm</span></li> <li>Tạo slide thuyết trình</li> <li>Tạo logo, banner, poster</li> </ol> <!-- B --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> B. Các ứng dụng A.I hỗ trợ công việc </p> <ol start="18" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Tạo ảnh avatar thương hiệu cá nhân</li> <li>Tạo ảnh từ câu lệnh (prompt)</li> <li>Tạo hình nền A.I sáng tạo</li> <li>Đổi khuôn mặt</li> <li>Tạo Video từ link sản phẩm</li> <li>Tạo Video từ văn bản</li> <li>Tạo Video từ hình ảnh</li> <li>Tạo Video cá nhân hoá nội dung</li> <li>Tạo Video MC ảo</li> <li>Ghép Video</li> <li>Tạo Giọng nói A.I, âm nhạc, bài hát từ văn bản</li> <li>Chat bot (Tích hợp GPT + Claude)</li> <li>Các trợ lý A.I</li> </ol> <!-- C --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> C. Giải pháp A.I nâng cao </p> <ol start="31" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li><span style="text-decoration: line-through; color: #999;">Xây dựng thương hiệu cá nhân</span></li> <li>Video Avatar</li> <li>Video hoạt hình</li> <li>Video postcard</li> <li><span style="text-decoration: line-through; color: #999;">Làm MV, video ca nhạc</span></li> <li>Tự chấm điểm nội dung quảng cáo</li> </ol> <!-- D --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> D. Hỗ trợ và mentor chuyên sâu </p> <ol start="37" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Đào tạo sử dụng A.I</li> <li>Tham gia cộng đồng A.I</li> </ol> </div>',
                'price' => 4990000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/ban-hang-tieu-chuan.html?utm_source=',
                'credit' => 500000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Premium'
            ],
            [
                'name' => 'A.I Bán Hàng',
                'duration' => '1 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <!-- A --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;"> A. Giải pháp A.I bán hàng </p> <ol style="padding-left: 20px; margin-top: 0; list-style-type: decimal; list-style-position: outside;"> <li>Thiết lập thông tin sản phẩm</li> <li>Phân tích chuyên sâu về sản phẩm</li> <li>Tạo status quảng cáo</li> <li>Tạo chiến dịch quảng cáo</li> <li><span style="text-decoration: line-through; color: #999;">Tạo thơ quảng cáo sản phẩm</span></li> <li><span style="text-decoration: line-through; color: #999;">Tạo nhạc quảng cáo sản phẩm</span></li> <li><span style="text-decoration: line-through; color: #999;">Tạo status fanpage 1 tháng</span></li> <li>Bổ sung thông tin nâng cao</li> <li>Phân tích chuyên sâu về doanh nghiệp</li> <li>Chiến lược nội dung ngắn hạn</li> <li>Xem kết quả và hiệu chỉnh: Nội dung, Mục tiêu, Phong cách, Cảm xúc, định dạng cho bài quảng cáo</li> <li>Tạo ảnh quảng cáo sản phẩm (Tự động tạo ảnh, Tải ảnh, Tạo ảnh theo yêu cầu)</li> <li>Tạo Video quảng cáo sản phẩm (Tích hợp tự động tạo Video, Tải Video, Tạo Video theo yêu cầu)</li> <li>Kết nối Fanpage và đặt lịch đăng</li> <li><span style="text-decoration: line-through; color: #999;">A.I AGENT tự động sản xuất nội dung, ảnh, Video và đăng bài fanpage cả tháng, năm</span></li> <li>Tạo slide thuyết trình</li> <li>Tạo logo, banner, poster</li> </ol> <!-- B --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> B. Các ứng dụng A.I hỗ trợ công việc </p> <ol start="18" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Tạo ảnh avatar thương hiệu cá nhân</li> <li>Tạo ảnh từ câu lệnh (prompt)</li> <li>Tạo hình nền A.I sáng tạo</li> <li>Đổi khuôn mặt</li> <li>Tạo Video từ link sản phẩm</li> <li>Tạo Video từ văn bản</li> <li>Tạo Video từ hình ảnh</li> <li>Tạo Video cá nhân hoá nội dung</li> <li>Tạo Video MC ảo</li> <li>Ghép Video</li> <li>Tạo Giọng nói A.I, âm nhạc, bài hát từ văn bản</li> <li>Chat bot (Tích hợp GPT + Claude)</li> <li>Các trợ lý A.I</li> </ol> <!-- C --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> C. Giải pháp A.I nâng cao </p> <ol start="31" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li><span style="text-decoration: line-through; color: #999;">Xây dựng thương hiệu cá nhân</span></li> <li>Video Avatar</li> <li>Video hoạt hình</li> <li>Video postcard</li> <li><span style="text-decoration: line-through; color: #999;">Làm MV, video ca nhạc</span></li> <li>Tự chấm điểm nội dung quảng cáo</li> </ol> <!-- D --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> D. Hỗ trợ và mentor chuyên sâu </p> <ol start="37" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Đào tạo sử dụng A.I</li> <li>Tham gia cộng đồng A.I</li> </ol> </div>',
                'price' => 699000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/ban-hang-tieu-chuan.html?utm_source=',
                'credit' => 100000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Premium'
            ],
            [
                'name' => 'Chăm Sóc Khách Hàng',
                'duration' => '12 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;"> A. Tích hợp Chatbot chăm sóc khách hàng </p> <ol style="padding-left: 20px; margin: 0; list-style-type: decimal; list-style-position: outside;"> <li>Thiết lập Chatbot A.I tự động CSKH 24/7</li> <li>Tự động tương tác, trả lời tư vấn, định hướng khách hàng đến chốt sale 24/7</li> <li>Tổng hợp, phân tích, báo cáo kết quả chăm sóc và bán hàng</li> </ol> </div>',
                'price' => 5000000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/cham-soc-khach-hang.html?utm_source=',
                'credit' => 500000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Premium'
            ],
            [
                'name' => 'Tìm Kiếm Khách Hàng',
                'duration' => '12 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <ol style="padding-left: 20px; margin: 0; list-style-type: decimal; list-style-position: outside;"> <li>Tự động tìm kiếm khách hàng tiềm năng theo yêu cầu</li> </ol> </div>',
                'price' => 300000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/tim-kiem-khach-hang.html?utm_source=',
                'credit' => 100000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Premium'
            ],
            [
                'name' => 'VIP',
                'duration' => '12 tháng',
                'content' => '<div style="font-size: 14px; line-height: 1.6; color: #000;"> <!-- A --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;"> A. GIẢI PHÁP A.I BÁN HÀNG </p> <ol style="padding-left: 20px; margin: 0; list-style-type: decimal; list-style-position: outside;"> <li>Thiết lập thông tin sản phẩm</li> <li>Phân tích chuyên sâu về sản phẩm</li> <li>Tạo status quảng cáo</li> <li>Tạo chiến dịch quảng cáo</li> <li>Tạo nhạc quảng cáo sản phẩm</li> <li>Tạo thơ quảng cáo sản phẩm</li> <li>Tạo status fanpage 1 tháng</li> <li>Bổ sung thông tin nâng cao</li> <li>Phân tích chuyên sâu về doanh nghiệp</li> <li>Chiến lược nội dung ngắn hạn</li> <li>Xem kết quả và hiệu chỉnh: Nội dung, Mục tiêu, Phong cách, Cảm xúc, định dạng cho bài quảng cáo</li> <li>Tạo ảnh quảng cáo sản phẩm (Tự động tạo ảnh, Tải ảnh, Tạo ảnh theo yêu cầu)</li> <li>Tạo video quảng cáo sản phẩm (Tự động tạo, tải, theo yêu cầu)</li> <li>Kết nối Fanpage và đặt lịch đăng</li> <li>AI AGENT tự động sản xuất nội dung, ảnh, video và đăng bài fanpage cố định, nhóm</li> <li>Tạo slide thuyết trình</li> <li>Tạo logo, banner, poster</li> </ol> <!-- B --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> B. CÁC ỨNG DỤNG A.I HỖ TRỢ CÔNG VIỆC </p> <ol start="18" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Tạo ảnh avatar thương hiệu cá nhân</li> <li>Tạo ảnh từ câu lệnh (prompt)</li> <li>Tạo hình nền A.I sáng tạo</li> <li>Đổi khuôn mặt</li> <li>Tạo video từ link sản phẩm</li> <li>Tạo video từ văn bản</li> <li>Tạo video từ hình ảnh</li> <li>Tạo video cá nhân hoá nội dung</li> <li>Tạo video MC ảo</li> <li>Ghép video</li> <li>Giọng nói A.I, âm nhạc, bài hát từ văn bản</li> <li>Chat bot (Tích hợp GPT + Claude)</li> <li>Các trợ lý A.I</li> </ol> <!-- C --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> C. GIẢI PHÁP A.I NÂNG CAO </p> <ol start="31" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Xây dựng thương hiệu cá nhân</li> <li>Video Avatar</li> <li>Video hoạt hình</li> <li>Video postcard</li> <li>Làm MV – video ca nhạc</li> <li>Tự chấm điểm nội dung quảng cáo</li> </ol> <!-- D --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> D. HỖ TRỢ VÀ MENTOR CHUYÊN SÂU </p> <ol start="37" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Đào tạo sử dụng A.I</li> <li>Tham gia cộng đồng A.I</li> </ol> <!-- E --> <p style="color: #0E7A94; font-weight: bold; text-transform: uppercase; margin-top: 16px; margin-bottom: 8px;"> E. TÍCH HỢP CHATBOT CHĂM SÓC KHÁCH HÀNG </p> <ol start="39" style="padding-left: 20px; list-style-type: decimal; list-style-position: outside;"> <li>Thiết lập Chatbot A.I tự động CSKH 24/7</li> <li>Tự động tương tác, trả lời tư vấn, định hướng khách hàng đến chốt sale 24/7</li> <li>Tổng hợp, phân tích, báo cáo kết quả chăm sóc và bán hàng</li> </ol> </div>',
                'price' => 14999000,
                'link' => 'https://aff.aiwow.com.vn/san-pham/aiwow-nang-cao.html?utm_source=',
                'credit' => 1500000,
                'title' => '<p style="color: #0E7A94; font-size: 16px; font-weight: 700;">Được sử dụng các giải pháp và các tính năng</p>',
                'template' => 'Premium'
            ],
        ];

        DB::table('credit_packages')->insert($data);
    }
}
