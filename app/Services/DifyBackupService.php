<?php

namespace App\Services;

use App\Models\AIBusinessProject;
use Illuminate\Support\Facades\Log;
use JsonException;

class DifyBackupService
{

    /**
* @throws JsonException
*/public function updateMetadata(AIBusinessProject $aiBusinessProject, array $query):void
    {
        if (!$aiBusinessProject->meta_data) {
            $aiBusinessProject = $this->generateMetadata($aiBusinessProject);
        }

        $metaData = json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR);
        $currentStep = $query['currentStep'] ?? '';

        if ($currentStep === 'buoc3') {
            $metaData['the_loai'] = $query['the_loai'] ?? '';
            $metaData['current_sub_step'] = $query['currentSubStep'] ?? '';
            $metaData['mode'] = $query['mode'] ?? '';
        }

        if ($currentStep === 'buoc4') {
            $metaData['muc_tieu'] = $query['muc_tieu'] ?? '';
        }
        $aiBusinessProject->meta_data = json_encode($metaData, JSON_THROW_ON_ERROR);
        $aiBusinessProject->save();
    }

    /**
    * @throws JsonException
    */
    public function generateMetadata(AIBusinessProject $aiBusinessProject): AIBusinessProject
    {
        $metaData = $aiBusinessProject->meta_data ? json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR) : [];
        $tenDuAn = trim("$aiBusinessProject->title, $aiBusinessProject->description", ", ");
        $metaData['ten_du_an'] = $tenDuAn;

        $aiBusinessProject->meta_data = json_encode($metaData, JSON_THROW_ON_ERROR);
        $aiBusinessProject->save();

        return $aiBusinessProject;
    }

    /**
    * @throws JsonException
    */
    public function backupDifyByChatGPT($currentStep, $aiBusinessProject):string
    {
        Log::info('Dify loi, tao thu cong voi chatgpt');
        $metaData = $aiBusinessProject->meta_data ? json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR) : [];
        // Call ChatGPT API
        if ($currentStep === 'buoc3' && $metaData['the_loai'] === 'Chiến dịch Truyền thông') {
            return "Quảng Bá Sản Phẩm/Dịch Vụ\nTăng Nhận Thức Thương Hiệu\nThúc Đẩy Tương Tác Khách Hàng\nTạo Lead và Chuyển Đổi\nTạo Dẫn Đường Cho Trang Đích\nXây Dựng và Nuôi Dưỡng Mối Quan Hệ Khách Hàng\nTối Ưu Hóa Cho SEO\nGiáo Dục Khách Hàng\nHỗ Trợ Chiến Dịch Marketing\nXây Dựng Cộng Đồng\nThúc Đẩy Bán Hàng và Tạo Doanh Thu\nPhản Hồi và Giải Quyết Vấn Đề của Khách Hàng\nPhá vỡ rào cản khách hàng\nTạo Nội Dung Viral\nPhát Triển Thương Hiệu Cá Nhân\nTạo Uy Tín và Chứng Minh Chuyên Môn\nTối Ưu Hóa Trải Nghiệm Khách Hàng\nThúc Đẩy Văn Hóa Doanh Nghiệp\nTác Động Xã Hội và Trách Nhiệm Cộng Đồng";
        }

        if ($currentStep === 'buoc3' && $metaData['mode'] === 'expert' && $metaData['current_sub_step'] === 'buoc3_1') {
            return "Tăng trưởng doanh số\nTăng nhận diện thương hiệu\nXây dựng niềm tin & cộng đồng\nThúc đẩy chuyển đổi (chốt sale)\nGiữ chân khách hàng cũ\nXây dựng cộng đồng bền vững\nTăng nhận diện thương hiệu nhanh\nThu hút lượt tương tác ban đầu\nChạy quảng cáo ngắn hạn\nRa mắt sản phẩm/dịch vụ mới\nTạo nội dung viral thu hút khách hàng\nTăng lượt theo dõi trên mạng xã hội\nTối ưu hóa nội dung cho sự kiện cụ thể\nChốt sale nhanh, tạo đơn hàng ngay\nThu hút khách hàng qua khuyến mãi\nKiểm tra phản ứng thị trường sản phẩm/dịch vụ\nĐịnh vị thương hiệu trên nền tảng số\nTạo dựng uy tín trong ngành\nKích thích khách hàng quay lại mua hàng\nCủng cố hình ảnh doanh nghiệp trước đối tác\nPhát triển nội dung mang tính dài hạn";
        }

        if ($currentStep === 'buoc3' && $metaData['mode'] === 'expert' && $metaData['current_sub_step'] === 'buoc3_2') {
            return "Bài viết Quảng cáo sản phẩm\nThơ Quảng cáo sản phẩm\nLời nhạc Quảng cáo sản phẩm\nPhân tích thị trường\nChiến dịch Truyền thông";
        }

        if ($currentStep === 'buoc4') {
            return $this->getContent($this->callStep4ChatGpt($aiBusinessProject));
        }

        return '';
    }

    private function callStep4ChatGpt($aiBusinessProject)
    {
        $metaData = $aiBusinessProject->meta_data ? json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR) : [];
        $dataJson = $aiBusinessProject->data_json;
        $thongTinDauVao = $dataJson['information_project']['answer'] ?? '';
        $categories = config('chatgpt.difyai.category');
        $theLoai = $metaData['the_loai'] ?? 0;
        $tenDuAn = $metaData['ten_du_an'] ?? '';
        $mucTieu = $metaData['muc_tieu'] ?? '';

        switch ($theLoai)
        {
            case 'Bài viết Quảng cáo sản phẩm':
            case 'Thơ Quảng cáo sản phẩm':
            case 'Chiến dịch Truyền thông':
                        $systemPrompt = $categories[$theLoai]['system_prompt'];
                        $userPrompt = $categories[$theLoai]['user_prompt'];
                        $code5 = $categories[$theLoai]['code_5'] ?? '';
                        $userPrompt = str_replace([
                                '{code_5_result}',
                                '{bai_viet_qc_result}',
                                '{thong_tin_dau_vao}',
                                '{ten_du_an}',
                                '{muc_tieu}'
                            ], [
                                $code5,
                                '',
                                $thongTinDauVao,
                                $tenDuAn,
                                $mucTieu
                            ], $userPrompt);
                return $this->callGpt($systemPrompt, $userPrompt);
            default:
                dd('not found');
        }

        return $this->callGpt($systemPrompt, $userPrompt);
    }

    private function callGpt($systemPrompt, $userPrompt)
    {
        $client = \OpenAI::client(config('openai.api_key'));

        $response = $client->chat()->create([
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

        return $response->choices[0]->message->content;
    }

    private function getContent($content)
    {
        $userPrompt = $content . 'Chú ý:
         - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
         - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
         - Trả lời bằng Tiếng Việt.';

        $prefix = '{
                               "Mục tiêu": [
                                   "Tăng Nhận Diện Thương Hiệu",
                                   "Thông Tin Sản Phẩm/Dịch Vụ",
                                   "Tạo trao đổi về Sản Phẩm / Dịch vụ",
                                   "Kích Thích Sự Tương Tác (Engagement)",
                                   "Quảng bá về Tiện ích và Thái độ phục vụ",
                                   "Tạo Độ Tin Cậy và Chuyên Môn",
                                   "Phá bỏ Rào cản và tạo động lực mua",
                                   "Kích thích hành động mua ngay",
                                   "Kết Nối và Phản Hồi Khách Hàng",
                                   "Xây Dựng Cộng Đồng",
                                   "Hướng Dẫn và Giáo Dục khách hàng",
                                   "Phân Tích Rào cản Khách Hàng",
                                   "Tạo Dựng Mối Quan Hệ Với Khách Hàng",
                                   "Xu Hướng Và Tương Lai",
                                   "Tăng Cường Hình Ảnh Thương Hiệu"
                               ],
                               "Cảm xúc": [
                                   "Tin tưởng",
                                   "Hứng khởi",
                                   "Khẩn cấp",
                                   "Hạnh phúc",
                                   "Tự hào",
                                   "Thấu hiểu",
                                   "Khao khát",
                                   "An tâm",
                                   "Truyền cảm hứng",
                                   "Tò mò",
                                   "Yêu thương",
                                   "Vui vẻ Hài hước",
                                   "Động lực",
                                   "Hoài niệm"
                               ],
                               "Phong cách": [
                                   "Trang Trọng",
                                   "Hấp Dẫn và Sáng Tạo",
                                   "Thư Giãn và Thân Thiện",
                                   "Chuyên Nghiệp",
                                   "Hài hước và vui vẻ",
                                   "Thể Thao và Năng Động",
                                   "Hướng Dẫn và Giảng Dạy",
                                   "Chất Lượng và Tinh Tế",
                                   "Ngắn Gọn và Súc Tích",
                                   "Chia Sẻ Kinh Nghiệm Cá Nhân",
                                   "Truyện Cười và Giải Trí",
                                   "Đánh Giá và So Sánh",
                                   "Cảm Xúc và Sâu Sắc",
                                   "Tone Nghiêm túc",
                                   "Tone Lạc quan",
                                   "Tone Hấp Dẫn và Thú vị",
                                   "Tone Thư Thái, nhẹ nhàng"
                               ],
                               "Định dạng": [
                                   "Tiêu đề hấp dẫn hơn",
                                   "Làm lại 1 bài ngắn hơn!",
                                   "Làm lại 1 bài dài hơn!"
                               ]
                           }
                           ';
        return $prefix . $this->callGpt('', $userPrompt);
    }
}
