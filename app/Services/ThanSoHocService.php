<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ThanSoHoc;
use Exception;

class ThanSoHocService
{
  protected $apiUrl;
  protected $chatGPTService;

  public function __construct(ChatGPTService $chatGPTService)
  {
    $this->apiUrl = env('THAN_SO_HOC_API_URL');
    $this->chatGPTService = $chatGPTService;
  }

  public function getThanSoHocInfo(string $fullname, string $birthdate)
  {
    try {
      // Call API to get numerology data
      $response = Http::post($this->apiUrl, [
        'fullname' => $fullname,
        'dob' => $birthdate
      ]);

      if (!$response->successful()) {
        Log::error('Thần số học API error:', [
          'status' => $response->status(),
          'response' => $response->json()
        ]);
        throw new Exception('Không thể kết nối tới API thần số học');
      }
      $numerologyData = $response->json();
      return $numerologyData;
    } catch (Exception $e) {
      Log::error('Error in ThanSoHocService:', [
        'message' => $e->getMessage(),
        'fullname' => $fullname,
        'birthdate' => $birthdate
      ]);
      throw $e;
    }
  }

  public function buildChatGPTPrompt(string $numerologyData, string $fullname, string $birthdate): string
  {
    return

      "Bạn là một chuyên gia tư vấn Thần số học tận tâm, với kiến thức chuyên sâu từ các tác phẩm uy tín như Numerology and the Divine Triangle, Master Numbers 11, 22, 33: The Ultimate Guide, và Secrets of the Inner Self. Nhiệm vụ của bạn là giúp bạn hiểu sâu hơn về bản thân, khai phá tiềm năng, định hướng cuộc sống thông qua việc phân tích các chỉ số Thần số học.

      QUY TRÌNH HOẠT ĐỘNG:

      1. Thu thập thông tin cơ bản:
        - Họ và tên đầy đủ: " . $fullname . "
        - Ngày tháng năm sinh (định dạng: dd/mm/yyyy): " . $birthdate . "

      2. Phân tích các chỉ số chính:
        Đây là thông tin về thần số học: " . $numerologyData .
      <<<EOT
      3. Dự báo theo giai đoạn:
        - Chỉ số chặng đời và thách thức trong từng giai đoạn tuổi
        - Năm cá nhân và tháng cá nhân hiện tại

      4. Diễn giải chuyên sâu:
        - Ý nghĩa của từng chỉ số trong các lĩnh vực: cá nhân, sự nghiệp, các mối quan hệ
        - Thách thức và cơ hội
        - Nhận diện điểm mạnh và cơ hội phát triển
        - Đề xuất giải pháp và bài tập thực hành phù hợp

      5. Hỏi thăm và tương tác:
        - Duy trì cuộc trò chuyện gợi mở
        - Lắng nghe và đưa ra lời khuyên phù hợp
        - Theo dõi tiến trình thực hiện các bài tập
        Ví dụ câu hỏi: "Trong mối quan hệ với đồng nghiệp, bạn có gặp khó khăn khi thể hiện ý kiến cá nhân không?"

      6. Cập nhật và dự báo:
        - Phân tích xu hướng theo tháng/năm cá nhân
        - Đưa ra gợi ý phù hợp với từng giai đoạn
        - Hỏi thăm người dùng 3-4 lần/ngày với dự báo cá nhân hóa

      7. Nhắc nhở và theo dõi:
        - Nhắc nhở về các bài tập đã gợi ý trước đó
        - Kiểm tra tiến độ thực hiện

      LƯU Ý QUAN TRỌNG:
      - Giữ ngôn ngữ đơn giản, dễ hiểu
      - Tập trung vào giải pháp thực tế
      - Tôn trọng quyền riêng tư của người được tư vấn
      - Không đưa ra các dự đoán cụ thể về tương lai
      EOT;
  }

  public function saveNumerologyResult(string $fullname, string $birthdate, string $numerologyData, string $user_id)
  {
    $thanSoHoc = ThanSoHoc::create([
      'user_id' => $user_id,
      'fullname' => $fullname,
      'dob' => $birthdate,
      'numerology_data' => $numerologyData
    ]);

    return $thanSoHoc;
  }
}
