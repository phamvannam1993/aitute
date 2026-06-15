<?php

namespace App\Services\DifyBackup;

use App\Models\AIBusinessProject;
use App\Services\DifyBackup\ChatGPT;
use App\Services\DifyBackup\DifyCreationStrategy;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * ExpertMode class for handling different processing steps in the application
 */
class StepHandler
{
	protected array $config;
	protected array $metaData = [];
	private string $currentStep;
	private ChatGPT $gptClient;
	public function __construct(
		protected array $inputData,
		protected AIBusinessProject $aiBusinessProject,
	) {
		$this->currentStep = $inputData['currentStep'] ?? '';
		$this->metaData = $aiBusinessProject->meta_data ? json_decode($aiBusinessProject->meta_data, true, 512, JSON_THROW_ON_ERROR) : [];
		$this->config = config('dify-prompt');
		$this->gptClient = new ChatGPT();
	}

	/**
	 * Process the current step
	 *
	 * @throws InvalidArgumentException
	 */
	public function handle(): string
	{
		$methodName = 'process' . ucfirst($this->currentStep);

		if (!method_exists($this, $methodName)) {
			throw new InvalidArgumentException("Invalid step: {$this->currentStep}");
		}

		return $this->{$methodName}();
	}

	/**
	 * Process step 3 of the workflow
	 */
	public function processBuoc3(): string
	{
		$metaData = $this->metaData;
		if (Arr::get($this->inputData, 'mode') === 'expert') {
			$subStep = Arr::get($metaData, 'current_sub_step') ?? '';
			$methodName = 'processExpert' . ucfirst($subStep);

			return $this->{$methodName}();
		}

		if ($metaData['the_loai'] === 'Nhạc Doanh nghiệp' && !$metaData['current_sub_step']) {
			return '[
				{
						"name": "goal",
						"label": "Mục tiêu chiến dịch",
						"type": "select",
						"options": [
							{
									"value": "Mục đích bài hát này là: Bán hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh lợi ích và giá trị của sản phẩm/dịch vụ, tạo động lực để khách hàng quyết định mua ngay.",
									"label": "Bán hàng"
							},
							{
									"value": "Mục đích bài hát này là: Giới thiệu thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật giá trị cốt lõi và hình ảnh thương hiệu, giúp khách hàng ghi nhớ.",
									"label": "Giới thiệu thương hiệu"
							},
							{
									"value": "Mục đích bài hát này là: Tạo tương tác. Hãy tạo ra 1 bài hát để có được mục đích cuối là khuyến khích khách hàng tham gia bình luận, chia sẻ hoặc thảo luận về sản phẩm/dịch vụ.",
									"label": "Tạo tương tác"
							},
							{
									"value": "Mục đích bài hát này là: Tăng nhận diện thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật thương hiệu với phong cách độc đáo, dễ nhớ.",
									"label": "Tăng nhận diện thương hiệu"
							},
							{
									"value": "Mục đích bài hát này là: Khuyến khích sử dụng dịch vụ. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh sự tiện lợi và lợi ích khi khách hàng chọn dịch vụ này.",
									"label": "Khuyến khích sử dụng dịch vụ"
							},
							{
									"value": "Mục đích bài hát này là: Kích thích tò mò. Hãy tạo ra 1 bài hát để có được mục đích cuối là khơi dậy sự tò mò của khách hàng, khiến họ muốn tìm hiểu thêm về sản phẩm/dịch vụ.",
									"label": "Kích thích tò mò"
							},
							{
									"value": "Mục đích bài hát này là: Chào mừng sự kiện. Hãy tạo ra 1 bài hát để có được mục đích cuối là để kỷ niệm hoặc quảng bá cho một sự kiện đặc biệt của thương hiệu.",
									"label": "Chào mừng sự kiện"
							},
							{
									"value": "Mục đích bài hát này là: Cảm ơn khách hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là thể hiện lòng biết ơn sâu sắc đối với khách hàng đã đồng hành cùng thương hiệu.",
									"label": "Cảm ơn khách hàng"
							},
							{
									"value": "Mục đích bài hát này là: Truyền cảm hứng. Hãy tạo ra 1 bài hát để có được mục đích cuối là mang đến động lực và niềm tin tích cực cho khách hàng.",
									"label": "Truyền cảm hứng"
							},
							{
									"value": "Mục đích bài hát này là: Giải trí. Hãy tạo ra 1 bài hát để có được mục đích cuối là với nội dung vui nhộn, nhẹ nhàng, tạo cảm giác thoải mái cho người đọc.",
									"label": "Giải trí"
							}
						]
				},
				{
						"name": "so_luong",
						"label": "Số lượng ý tưởng",
						"type": "select",
						"options": [
							{
									"value": "1",
									"label": "1"
							},
							{
									"value": "2",
									"label": "2"
							},
							{
									"value": "3",
									"label": "3"
							},
							{
									"value": "4",
									"label": "4"
							},
							{
									"value": "5",
									"label": "5"
							},
							{
									"value": "6",
									"label": "6"
							},
							{
									"value": "7",
									"label": "7"
							},
							{
									"value": "8",
									"label": "8"
							},
							{
									"value": "9",
									"label": "9"
							},
							{
									"value": "10",
									"label": "10"
							}
						]
				}
			]';
		}

		if ($metaData['the_loai'] === 'Nhạc Doanh nghiệp' && $metaData['current_sub_step']) {
			$functionName = 'processNhacDoanhNghiep' . ucfirst($metaData['current_sub_step']);
			return $this->{$functionName}();
		}

		if ($metaData['the_loai'] === 'Chiến dịch Truyền thông') {
			return "Quảng Bá Sản Phẩm/Dịch Vụ\nTăng Nhận Thức Thương Hiệu\nThúc Đẩy Tương Tác Khách Hàng\nTạo Lead và Chuyển Đổi\nTạo Dẫn Đường Cho Trang Đích\nXây Dựng và Nuôi Dưỡng Mối Quan Hệ Khách Hàng\nTối Ưu Hóa Cho SEO\nGiáo Dục Khách Hàng\nHỗ Trợ Chiến Dịch Marketing\nXây Dựng Cộng Đồng\nThúc Đẩy Bán Hàng và Tạo Doanh Thu\nPhản Hồi và Giải Quyết Vấn Đề của Khách Hàng\nPhá vỡ rào cản khách hàng\nTạo Nội Dung Viral\nPhát Triển Thương Hiệu Cá Nhân\nTạo Uy Tín và Chứng Minh Chuyên Môn\nTối Ưu Hóa Trải Nghiệm Khách Hàng\nThúc Đẩy Văn Hóa Doanh Nghiệp\nTác Động Xã Hội và Trách Nhiệm Cộng Đồng";
		}

		return '';
	}

	public function processNhacDoanhNghiepBuoc3_1(): string
	{
		$nhacDoanhNghiepCategory = $this->config['step4_prompts']['categories']['Nhạc Doanh nghiệp'];
		$systemPrompt = $nhacDoanhNghiepCategory['create_big_idea_system_prompt'];
		$userPrompt = $nhacDoanhNghiepCategory['create_big_idea_user_prompt'];
		$thongTinDauVao = $this->aiBusinessProject->data_json['information_project']['answer'] ?? '';

		$systemPrompt = str_replace([
			'{goal}',
			'{thong_tin_dau_vao}',
			'{ten_du_an}',
			'{so_luong}',
		], [
			$this->metaData['goal'] ?? '',
			$thongTinDauVao ?? '',
			$this->metaData['ten_du_an'] ?? '',
			$this->metaData['so_luong'] ?? ''
		], $systemPrompt);
		$userPrompt = str_replace([
			'{goal}',
			'{thong_tin_dau_vao}',
			'{so_luong}',
		], [
			$this->metaData['goal'] ?? '',
			$thongTinDauVao ?? '',
			$this->metaData['so_luong'] ?? ''
		], $userPrompt);

		$response = $this->gptClient->sendRequest($systemPrompt, $userPrompt);
		$response = str_replace(['json', '```'], '', $response);

		return $response;
	}

	public function processNhacDoanhNghiepBuoc3_2(): string
	{
		return '[
    {
        "name": "song_style",
        "label": "Phong cách bài hát",
        "type": "select",
        "options": [
            {"value": "Hài hước", "label": "Hài hước"},
            {"value": "Lãng mạn", "label": "Lãng mạn"},
            {"value": "Cảm động", "label": "Cảm động"},
            {"value": "Sôi động", "label": "Sôi động"},
            {"value": "Cổ điển", "label": "Cổ điển"},
            {"value": "Hiện đại", "label": "Hiện đại"},
            {"value": "Trang trọng", "label": "Trang trọng"},
            {"value": "Thân thiện", "label": "Thân thiện"},
            {"value": "Tinh nghịch", "label": "Tinh nghịch"},
            {"value": "Giản dị", "label": "Giản dị"}
        ]
    },
    {
        "name": "song_emotion",
        "label": "Cảm xúc bài hát",
        "type": "select",
        "options": [
            {"value": "Tin tưởng", "label": "Tin tưởng"},
            {"value": "Hứng khởi", "label": "Hứng khởi"},
            {"value": "Khẩn cấp", "label": "Khẩn cấp"},
            {"value": "Hạnh phúc", "label": "Hạnh phúc"},
             {"value": "Tự hào", "label": "Tự hào"},
            {"value": "Thấu hiểu", "label": "Thấu hiểu"},
             {"value": "Khao khát", "label": "Khao khát"},
            {"value": "An tâm", "label": "An tâm"},
            {"value": "Truyền cảm hứng", "label": "Truyền cảm hứng"},
            {"value": "Tò mò", "label": "Tò mò"},
            {"value": "Yêu thương", "label": "Yêu thương"},
           {"value": "Hài hước", "label": "Hài hước"},
            {"value": "Hoài niệm", "label": "Hoài niệm"},
            {"value": "Cảm động", "label": "Cảm động"}
        ]
    },
    {
        "name": "goal",
        "label": "Mục đích bài hát",
        "type": "select",
        "options": [
            {"value": "Bán hàng", "label": "Bán hàng"},
             {"value": "Giới thiệu thương hiệu", "label": "Giới thiệu thương hiệu"},
            {"value": "Tạo tương tác", "label": "Tạo tương tác"},
             {"value": "Tăng nhận diện thương hiệu", "label": "Tăng nhận diện thương hiệu"},
           {"value": "Khuyến khích sử dụng dịch vụ", "label": "Khuyến khích sử dụng dịch vụ"},
           {"value": "Kích thích tò mò", "label": "Kích thích tò mò"},
            {"value": "Chào mừng sự kiện", "label": "Chào mừng sự kiện"},
            {"value": "Cảm ơn khách hàng", "label": "Cảm ơn khách hàng"},
            {"value": "Truyền cảm hứng", "label": "Truyền cảm hứng"},
             {"value": "Giải trí", "label": "Giải trí"}
        ]
    },
    {
        "name": "key_message",
        "label": "Thông điệp chính",
        "type": "textarea",
        "placeholder": "Thông điệp chính cần nhấn mạnh" ,
        "value": ""
    },
    {
        "name": "keywords",
        "label": "Từ khóa (key)",
        "type": "text",
        "placeholder": "Từ khóa hoặc những từ/câu đặc biệt muốn có trong bài hát? Hãy viết ngắn gọn, cách nhau dấu phẩy",
        "value": ""
    },
    {
        "name": "trending_context",
        "label": "Bối cảnh",
        "type": "text",
        "placeholder": "Bối cảnh là xu hướng hoặc sự kiện để bài viết Chạm được tới tâm trí đám đông nhanh nhất và dễ nhất. Nó là thứ mà đám đông khách hàng đang quan tâm ở thời điểm này.",
        "value": ""
    }
]';
	}

	public function processExpertBuoc3_1(): string
	{
		$parts = [];
		$data = $this->inputData;

		$fields = [
			'company_name' => 'Tên Doanh Nghiệp là',
			'industry' => 'Lĩnh vực hoạt động là',
			'company_size' => 'Quy mô doanh nghiệp là',
			'business_model' => 'Mô hình doanh nghiệp là',
			'usp' => 'USP - Điểm khác biệt của doanh nghiệp là',
			'price_range' => 'Mức giá sản phẩm/dịch vụ là',
			'brand_positioning' => 'Định vị thương hiệu là'
		];

		foreach ($fields as $key => $label) {
			if (!empty($data[$key])) {
				$parts[] = "{$label}: {$data[$key]}";
			}
		}

		$this->metaData['phan_tich_doanh_nghiep'] = !empty($parts) ? implode(".\n", $parts) . "." : "";
		$this->saveMetaData();

		return implode("\n", $this->config['step3_responses']['buoc3_1'] ?? []);
	}

	public function processExpertBuoc3_2(): string
	{
		$this->metaData['muc_tieu'] = $this->inputData['muc_tieu'] ?? '';
		$this->saveMetaData();

		return implode("\n", $this->config['step3_responses']['buoc3_2'] ?? []);
	}

	private function saveMetaData()
	{
		$this->aiBusinessProject->meta_data = json_encode($this->metaData);
		$this->aiBusinessProject->save();
	}

	/**
	 * Process step 4 of the workflow
	 */
	public function processBuoc4(): string
	{
		$this->generateThongTinBoSungNangCaoPrompt();
		$userPrompt = $this->generatePromptBuoc4();

		return $this->getContentBuoc4($userPrompt);
	}

	/**
	 * Get content with GPT processing
	 */
	private function getContentBuoc4(string $prompt): string
	{
		if (!isset($this->gptClient)) {
			throw new InvalidArgumentException('GPT client is required for content processing');
		}

		$userPrompt = $prompt . $this->config['gpt_prompt_suffix'];
		$prefix = json_encode($this->config['content_structure'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

		return $prefix . $this->gptClient->sendRequest('', $userPrompt);
	}

	private function generatePromptBuoc4()
	{
		$theLoai = $this->metaData['the_loai'] ?? '';
		$categories = $this->config['step4_prompts']['categories'];
		if (!isset($categories[$theLoai])) {
			throw new InvalidArgumentException("Invalid category: {$theLoai}");
		}

		$category = $categories[$theLoai];
		$systemPrompt = $category['system_prompt'];
		$userPrompt = $category['user_prompt'];
		$code5 = $category['code_5'] ?? '';

		// Replace placeholders in user prompt
		$userPrompt = str_replace([
			'{code_5_result}',
			'{thong_tin_bo_sung_nang_cao}',
			'{bai_viet_qc_result}',
			'{thong_tin_dau_vao}',
			'{ten_du_an}',
			'{muc_tieu}',
			'{y_tuong}',
			'{phan_tich_doanh_nghiep}'
		], [
			$code5,
			Arr::get($this->metaData, 'thong_tin_bo_sung_nang_cao', ''),
			'',
			Arr::get($this->aiBusinessProject->data_json, 'information_project.answer', ''),
			Arr::get($this->metaData, 'ten_du_an', ''),
			Arr::get($this->metaData, 'muc_tieu', ''),
			Arr::get($this->metaData, 'y_tuong', ''),
			Arr::get($this->metaData, 'phan_tich_doanh_nghiep', '')
		], $userPrompt);

		return $this->gptClient->sendRequest($systemPrompt, $userPrompt);
	}

	private function generateThongTinBoSungNangCaoPrompt(): void
	{
		if ($this->metaData['the_loai'] === 'Bài viết Quảng cáo sản phẩm') {
			$this->metaData['thong_tin_bo_sung_nang_cao'] = $this->generateThongTinBoSungNangCao1Prompt();
		}

		if ($this->metaData['the_loai'] === 'Thơ Quảng cáo sản phẩm') {
			$this->metaData['thong_tin_bo_sung_nang_cao'] = $this->generateThongTinBoSungNangCao2Prompt();
		}

		if ($this->metaData['the_loai'] === 'Nhạc Doanh nghiệp' || $this->metaData['the_loai'] === 'Lời nhạc Quảng cáo sản phẩm') {
			$this->metaData['thong_tin_bo_sung_nang_cao'] = $this->generateThongTinBoSungNangCao3Prompt();
		}

		if ($this->metaData['the_loai'] === 'Chiến dịch Truyền thông') {
			$this->metaData['thong_tin_bo_sung_nang_cao'] = $this->generateThongTinBoSungNangCao4Prompt();
		}

		$this->saveMetaData();
	}

	private function generateThongTinBoSungNangCao1Prompt(): string
	{
		try {
			$data = $this->inputData;

			$parts = [];

			// Target Audience
			if (!empty($data['target_audience'])) {
				$parts[] = "Đối tượng khách hàng (Target Audience) của bài viết này là: " . $data['target_audience'];
			}

			// Goal
			if (!empty($data['goal'])) {
				$goalMapping = [
					"Tăng Nhận Diện Thương Hiệu" => "Tạo một status nói về phần 'Câu chuyện về Thương hiệu' và nhấn mạnh 'Ưu điểm và giá trị độc đáo (USP)' với những tính năng độc đáo của sản phẩm/dịch vụ, làm nổi bật sự khác biệt và giá trị nổi bật.",
					"Thông Tin Sản Phẩm/Dịch Vụ" => "Tạo một status về tính năng của sản phẩm/dịch vụ, nói tới 'Tính năng và giải quyết vấn đề', nhấn mạnh 'Ưu điểm và giá trị độc đáo (USP)', làm nổi bật 'lợi ích cho khách hàng', cách sản phẩm/dịch vụ giải quyết vấn đề cụ thể, tiết kiệm thời gian, tiền bạc, và giải thích nếu không có sản phẩm, khách hàng sẽ gặp những khó khăn gì.",
					"Tạo trao đổi về Sản Phẩm/Dịch Vụ" => "Viết một status kêu gọi cộng đồng thảo luận về kinh nghiệm sử dụng sản phẩm/dịch vụ, khuyến khích khách hàng chia sẻ phản hồi và góp ý về sản phẩm/dịch vụ, thể hiện sự lắng nghe và cam kết cải tiến.",
					"Kích Thích Sự Tương Tác (Engagement)" => "Tạo một bài viết với mục tiêu thu hút nhiều tương tác, sử dụng chủ đề hấp dẫn và có sức lan tỏa, khuyến khích sự tham gia tự nhiên từ cộng đồng.",
					"Quảng bá về Tiện ích và Thái độ phục vụ" => "Tạo bài viết thông báo về 'Dịch vụ hỗ trợ', nhấn mạnh thái độ chăm sóc khách hàng, tiện ích phục vụ, 'Kênh hỗ trợ khách hàng', hệ thống giao hàng và phân phối, thể hiện sự thuận tiện và nhiệt tình.",
					"Tạo Độ Tin Cậy và Chuyên Môn" => "Viết một status chia sẻ rõ hơn về 'Chứng nhận và thông tin tin cậy', lợi ích khách hàng từ sản phẩm/dịch vụ, cùng với sự hỗ trợ nhiệt tình từ các 'Kênh hỗ trợ khách hàng'.",
					"Phá bỏ Rào cản và Tạo động lực mua" => "Liệt kê 5 rào cản lớn nhất của khách hàng với sản phẩm/dịch vụ, viết một status kể câu chuyện về một trong các rào cản này, giải thích cách sản phẩm giúp vượt qua rào cản, khuyến khích hành động.",
					"Kích thích hành động mua ngay" => "Viết một status dựa trên 'Ưu điểm và giá trị độc đáo (USP)', tạo sự hứng thú và kỳ vọng, nhấn mạnh 'Chương trình khuyến mại' và phản hồi tích cực từ khách hàng, kết hợp lời kêu gọi hành động mạnh mẽ như 'Đặt hàng ngay'.",
					"Kết Nối và Phản Hồi Khách Hàng" => "Viết một status hiển thị phản hồi tích cực từ khách hàng, nhấn mạnh sự hài lòng của họ, mời khách hàng chia sẻ trải nghiệm để tăng kết nối.",
					"Xây Dựng Cộng Đồng" => "Tạo bài viết về 'Cộng đồng' của sản phẩm/dịch vụ, lợi ích mà cộng đồng mang lại, khuyến khích tham gia các hoạt động hoặc sự kiện, nhấn mạnh mối liên hệ với các mục tiêu xã hội.",
					"Hướng Dẫn và Giáo Dục Khách Hàng" => "Viết bài hướng dẫn sử dụng sản phẩm từ 'Cách sử dụng sản phẩm', cung cấp thông tin hữu ích và giải thích lợi ích khi sử dụng đúng cách.",
					"Phân Tích Rào cản Khách Hàng" => "Viết bài tự thống kê các rào cản mà khách hàng gặp phải, khuyến khích chia sẻ vướng mắc để hiểu thêm về nhu cầu.",
					"Tạo Dựng Mối Quan Hệ Với Khách Hàng" => "Viết bài chia sẻ phản hồi khách hàng, kể câu chuyện sử dụng sản phẩm, nhấn mạnh điểm mạnh trong dịch vụ hỗ trợ và khuyến khích tương tác.",
					"Xu Hướng Và Tương Lai" => "Viết status kết nối sản phẩm/dịch vụ với xu hướng hoặc sự kiện phổ biến, thể hiện tầm nhìn và mục tiêu của thương hiệu.",
					"Tăng Cường Hình Ảnh Thương Hiệu" => "Tạo một status thể hiện giá trị và văn hóa thương hiệu qua sản phẩm/dịch vụ, nhấn mạnh cam kết chất lượng, trách nhiệm xã hội và sự sáng tạo."
				];
				if (isset($goalMapping[$data['goal']])) {
					$parts[] = $goalMapping[$data['goal']];
				}
			}

			// Key Message
			if (!empty($data['key_message'])) {
				$parts[] = "Thông điệp chính (Key Message) của bài viết sẽ là: " . $data['key_message'];
			}

			// Desired Emotion
			if (!empty($data['desired_emotion'])) {
				$desiredEmotionMapping = [
					"Tin tưởng" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tin tưởng. Hãy viết bài quảng cáo tập trung vào việc xây dựng niềm tin, nhấn mạnh chứng nhận, đánh giá từ khách hàng và uy tín của sản phẩm.",
					"Hứng khởi" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hứng khởi. Hãy tạo bài viết làm nổi bật sự mới lạ, độc đáo của sản phẩm, khiến khách hàng cảm thấy hứng khởi và muốn khám phá ngay.",
					"Khẩn cấp" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khẩn cấp. Viết bài kèm thông điệp khẩn cấp, sử dụng ngôn ngữ nhấn mạnh ưu đãi có thời hạn hoặc số lượng giới hạn để thúc đẩy khách hàng hành động.",
					"Hạnh phúc" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là niềm hạnh phúc. Hãy viết bài mô tả cảm giác hạnh phúc và niềm vui mà khách hàng có thể trải nghiệm khi sử dụng sản phẩm/dịch vụ.",
					"Tự hào" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tự hào. Tạo bài viết nhấn mạnh giá trị đẳng cấp và lý do khách hàng sẽ cảm thấy tự hào khi sử dụng sản phẩm.",
					"Thấu hiểu" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự thấu hiểu. Hãy tạo bài viết thể hiện sự đồng cảm với khó khăn hoặc nhu cầu của khách hàng.",
					"Khao khát" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khao khát. Viết bài tập trung vào việc khơi gợi khao khát của khách hàng bằng cách nhấn mạnh giá trị và lợi ích độc đáo của sản phẩm.",
					"An tâm" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự an tâm. Tạo nội dung làm rõ các cam kết, bảo hành, và chứng minh tính an toàn, hiệu quả của sản phẩm.",
					"Truyền cảm hứng" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự cảm hứng. Hãy viết bài mang tính truyền cảm hứng, nhấn mạnh những thay đổi tích cực mà sản phẩm/dịch vụ có thể mang lại.",
					"Tò mò" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tò mò. Viết bài mở đầu với một câu chuyện hoặc dữ liệu thú vị, làm khách hàng tò mò và muốn tìm hiểu chi tiết hơn.",
					"Yêu thương" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự yêu thương. Hãy tạo bài viết làm nổi bật cách sản phẩm/dịch vụ mang lại sự yêu thương, chăm sóc cho bản thân hoặc gia đình.",
					"Vui vẻ Hài hước" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hài hước. Hãy viết bài quảng cáo hài hước và dí dỏm để tạo sự thích thú và dễ dàng chia sẻ trên mạng xã hội.",
					"Động lực" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự động lực. Hãy viết bài quảng cáo mang tính khích lệ, tập trung vào việc thúc đẩy khách hàng thực hiện hành động cụ thể ngay lập tức.",
					"Hoài niệm" => "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hoài niệm. Viết bài quảng cáo gợi nhớ những kỷ niệm đẹp trong quá khứ và kết nối cảm xúc với sản phẩm/dịch vụ."
				];
				if (isset($desiredEmotionMapping[$data['desired_emotion']])) {
					$parts[] = $desiredEmotionMapping[$data['desired_emotion']];
				}
			}

			// Trending Context
			if (!empty($data['trending_context'])) {
				$parts[] = "Status được viết trong bối cảnh hoặc trending mà đám đông đang quan tâm là: " . $data['trending_context'];
			}

			// Writing Style
			if (!empty($data['writing_style'])) {
				$writingStyleMapping = [
					"Trang Trọng" => "Phong cách viết: Tạo ra một phong cách trang trọng cho nội dung, phù hợp với môi trường kinh doanh chính thức hoặc các sự kiện quan trọng.",
					"Hấp Dẫn và Sáng Tạo" => "Phong cách viết: Làm cho nội dung trở nên sáng tạo và độc đáo, thu hút sự chú ý bằng cách thể hiện một cách tiếp cận mới lạ.",
					"Thư Giãn và Thân Thiện" => "Phong cách viết: Tạo nội dung để nó trở nên thân thiện và dễ chịu, tạo môi trường thư giãn cho người đọc.",
					"Chuyên Nghiệp" => "Phong cách viết: Tăng cường tính chuyên nghiệp và uy tín của nội dung, sử dụng ngôn ngữ chính xác và thông tin đáng tin cậy.",
					"Hài hước và vui vẻ" => "Phong cách viết: Thêm yếu tố hài hước và niềm vui vào nội dung, làm cho nó trở nên sinh động và giải trí.",
					"Thể Thao và Năng Động" => "Phong cách viết: Phản ánh tính năng động và sôi động, thích hợp với những sản phẩm liên quan đến thể thao hoặc hoạt động ngoài trời.",
					"Hướng Dẫn và Giảng Dạy" => "Phong cách viết: Đưa nội dung thành một hướng dẫn hoặc bài giảng, cung cấp thông tin hữu ích và kiến thức cho người đọc.",
					"Chất Lượng và Tinh Tế" => "Phong cách viết: Tăng cường chất lượng và tinh tế trong nội dung, nhấn mạnh sự chăm chút và độ chính xác của thông tin.",
					"Ngắn Gọn và Súc Tích" => "Phong cách viết: Làm cho nội dung trở nên ngắn gọn và đến trực tiếp điểm, loại bỏ mọi thông tin thừa.",
					"Chia Sẻ Kinh Nghiệm Cá Nhân" => "Phong cách viết: Thêm vào nội dung những trải nghiệm cá nhân hoặc câu chuyện từ người viết, làm cho nó trở nên thực tế và có liên quan hơn.",
					"Truyện Cười và Giải Trí" => "Phong cách viết: Biến nội dung thành một phần giải trí, thêm truyện cười hoặc yếu tố giải trí để thu hút người đọc.",
					"Đánh Giá và So Sánh" => "Phong cách viết: Thêm phần đánh giá hoặc so sánh sản phẩm/dịch vụ với các lựa chọn khác, cung cấp thông tin chi tiết và khách quan.",
					"Cảm Xúc và Sâu Sắc" => "Phong cách viết: Tạo nội dung với cảm xúc sâu sắc, sử dụng ngôn từ mô tả cảm xúc và trải nghiệm đa chiều.",
					"Tone Nghiêm túc" => "Phong cách viết: Nội dung nghiêm túc và uy tín, thích hợp cho các thông điệp quan trọng.",
					"Tone Lạc quan" => "Phong cách viết: Làm cho nội dung trở nên lạc quan và tích cực hơn, truyền đạt sự tự tin và khả năng của sản phẩm.",
					"Tone Hấp Dẫn và Thú vị" => "Phong cách viết: Nội dung hấp dẫn và thú vị, thu hút sự chú ý của người đọc.",
					"Tone Thư Thái, nhẹ nhàng" => "Phong cách viết: Nội dung tạo cảm giác thư thái và nhẹ nhàng, giúp người đọc cảm thấy thoải mái khi tiếp nhận thông tin."
				];
				if (isset($writingStyleMapping[$data['writing_style']])) {
					$parts[] = $writingStyleMapping[$data['writing_style']];
				}
			}

			// Post Format
			if (!empty($data['post_format'])) {
				$postFormatMapping = [
					"Status ngắn" => "Định dạng bài viết là: Status ngắn. Hãy viết một bài quảng cáo ngắn gọn, thu hút, truyền tải thông điệp chính trong 2-5 câu.",
					"Bài viết dài" => "Định dạng bài viết là: Bài viết dài. Hãy viết một bài quảng cáo chi tiết, kể câu chuyện liên quan đến sản phẩm/dịch vụ, làm nổi bật giá trị và khơi gợi cảm xúc của khách hàng.",
					"Dạng Danh sách" => "Định dạng bài viết là: Danh sách. Hãy viết một bài quảng cáo dạng danh sách, làm nổi bật [số lượng] lý do khách hàng nên chọn sản phẩm/dịch vụ này.",
					"Câu chuyện kể" => "Định dạng bài viết là: Câu chuyện. Hãy viết bài quảng cáo dưới dạng kể chuyện, bắt đầu bằng một tình huống thực tế để thu hút sự quan tâm và dẫn dắt đến giá trị của sản phẩm/dịch vụ.",
					"Kịch bản Video quảng cáo" => "Định dạng bài viết là: Video quảng cáo. Hãy viết kịch bản quảng cáo video dài [số giây] giây, làm nổi bật giá trị chính của sản phẩm/dịch vụ với thông điệp dễ nhớ và hấp dẫn.",
					"Câu hỏi thảo luận" => "Định dạng bài viết là: Câu hỏi thảo luận. Hãy viết bài quảng cáo dạng câu hỏi để thu hút bình luận của khách hàng, liên quan đến sản phẩm hoặc chủ đề họ quan tâm.",
					"Trích dẫn" => "Định dạng bài viết là: Trích dẫn. Hãy tạo một câu trích dẫn ấn tượng về sản phẩm/dịch vụ, ngắn gọn và dễ ghi nhớ.",
					"Hướng dẫn sử dụng" => "Định dạng bài viết là: Hướng dẫn sử dụng. Hãy viết bài quảng cáo dưới dạng hướng dẫn từng bước cách sử dụng sản phẩm/dịch vụ để đạt hiệu quả tốt nhất.",
					"So sánh" => "Định dạng bài viết là: So sánh. Hãy viết bài quảng cáo so sánh sản phẩm/dịch vụ này với các đối thủ, làm nổi bật các ưu điểm và sự khác biệt.",
					"Feedback từ khách hàng" => "Định dạng bài viết là: Feedback từ khách hàng. Hãy viết một bài quảng cáo dựa trên phản hồi của khách hàng, làm nổi bật trải nghiệm tích cực và lợi ích họ đạt được.",
					"Câu chuyện khách hàng" => "Định dạng bài viết là: Câu chuyện khách hàng. Hãy viết bài quảng cáo kể lại câu chuyện của một khách hàng sử dụng sản phẩm/dịch vụ, tập trung vào những thay đổi tích cực trong cuộc sống của họ.",
					"Bài viết dựa trên số liệu" => "Định dạng bài viết là: Bài viết dựa trên số liệu. Hãy viết bài quảng cáo tập trung vào các số liệu thống kê hoặc dữ liệu thực tế để chứng minh hiệu quả của sản phẩm/dịch vụ."
				];
				if (isset($postFormatMapping[$data['post_format']])) {
					$parts[] = $postFormatMapping[$data['post_format']];
				}
			}

			// Promotion Gift
			if (!empty($data['promotion_gift'])) {
				$parts[] = "Status được viết trong bối cảnh hoặc trending mà đám đông đang quan tâm là: " . $data['promotion_gift'];
			}

			// CTA
			if (!empty($data['cta'])) {
				$ctaMapping = [
					"Mua Ngay" => "Thêm CTA 'Mua Ngay' với liên kết trực tiếp đến trang mua hàng, kèm theo lợi ích cụ thể khi mua sản phẩm.",
					"Khuyến khích liên kết và tương tác" => "Bổ sung CTA khuyến khích người đọc tương tác, như 'Bình luận ý kiến của bạn' hoặc 'Chia sẻ với bạn bè'.",
					"Đăng Ký tham gia" => "Thêm lời kêu gọi đăng ký tham gia sự kiện hoặc nhận bản tin, với hướng dẫn cụ thể về cách thức đăng ký.",
					"Xem Chi Tiết" => "CTA 'Xem Chi Tiết' với liên kết dẫn đến trang cung cấp thông tin chi tiết hơn về sản phẩm hoặc dịch vụ.",
					"Liên Hệ Chúng Tôi" => "Thêm thông tin liên hệ như địa chỉ email, số điện thoại hoặc form liên hệ trực tuyến để khách hàng có thể liên hệ dễ dàng.",
					"Thêm Chia Sẻ" => "Khuyến khích người đọc chia sẻ nội dung, có thể thông qua CTA như 'Chia sẻ nếu bạn thấy hữu ích'.",
					"Tăng bình luận" => "Thêm CTA kích thích người đọc bình luận, như 'Hãy cho chúng tôi biết suy nghĩ của bạn về vấn đề này'."
				];
				if (isset($ctaMapping[$data['cta']])) {
					$parts[] = $ctaMapping[$data['cta']];
				}
			}

			return !empty($parts) ? implode(".\n", $parts) . "." : "";
		} catch (\Exception $e) {
			return "Error processing content structure";
		}
	}

	private function generateThongTinBoSungNangCao2Prompt(): string
	{
		try {
			$data = $this->inputData;

			$parts = [];

			// Poem Style
			if (!empty($data['poem_style'])) {
				$poemStyleMapping = [
					"Hài hước" => "Phong cách bài thơ này là: Hài hước. Hãy sử dụng ngôn từ trong bài thơ mang phong cách hài hước, sử dụng ngôn ngữ dí dỏm và vui nhộn để tạo cảm giác thoải mái và giải trí cho người đọc.",
					"Lãng mạn" => "Phong cách bài thơ này là: Lãng mạn. Hãy sử dụng ngôn ngữ trong bài thơ mang phong cách lãng mạn, sử dụng từ ngữ bay bổng, nhẹ nhàng, tạo cảm giác tình cảm sâu sắc và tinh tế.",
					"Cảm động" => "Phong cách bài thơ này là: Cảm động. Hãy sử dụng ngôn ngữ trong bài thơ mang phong cách cảm động, sử dụng ngôn từ xúc động, sâu sắc để chạm đến trái tim người đọc.",
					"Sôi động" => "Phong cách bài thơ này là: Sôi động. Hãy sử dụng ngôn từ trong bài thơ mang phong cách sôi động, sử dụng ngôn ngữ mạnh mẽ, đầy năng lượng để truyền cảm hứng và động lực.",
					"Cổ điển" => "Phong cách bài thơ này là: Cổ điển. Hãy sử dụng ngôn từ trong bài thơ mang phong cách cổ điển, sử dụng ngôn từ truyền thống, mượt mà và gợi nhớ phong vị văn hóa xưa.",
					"Hiện đại" => "Phong cách bài thơ này là: Hiện đại. Hãy sử dụng ngôn từ trong bài thơ mang phong cách hiện đại, sử dụng ngôn ngữ trẻ trung, phá cách và gần gũi với xu hướng ngày nay.",
					"Trang trọng" => "Phong cách bài thơ này là: Trang trọng. Hãy sử dụng ngôn từ trong bài thơ mang phong cách trang trọng, sử dụng ngôn từ lịch sự, nghiêm túc, phù hợp với bối cảnh chuyên nghiệp.",
					"Thân thiện" => "Phong cách bài thơ này là: Thân thiện. Hãy sử dụng ngôn từ trong bài thơ mang phong cách thân thiện, sử dụng ngôn ngữ gần gũi, dễ hiểu và tạo cảm giác gần gũi với người đọc.",
					"Tinh nghịch" => "Phong cách bài thơ này là: Tinh nghịch. Hãy sử dụng ngôn từ trong bài thơ mang phong cách tinh nghịch, sử dụng ngôn ngữ trẻ trung, hài hước và phù hợp với những tình huống vui nhộn.",
					"Giản dị" => "Phong cách bài thơ này là: Giản dị. Hãy sử dụng ngôn từ trong bài thơ mang phong cách giản dị, sử dụng ngôn ngữ đơn giản, chân thực, phù hợp với đời sống thường ngày."
				];
				if (isset($poemStyleMapping[$data['poem_style']])) {
					$parts[] = $poemStyleMapping[$data['poem_style']];
				}
			}

			// Poem Emotion
			if (!empty($data['poem_emotion'])) {
				$poemEmotionMapping = [
					"Tin tưởng" => "Cảm xúc bài thơ này là: Tin tưởng. Hãy làm sao để bài thơ này mang cảm giác tin cậy, sử dụng ngôn từ thể hiện sự an tâm và uy tín của sản phẩm/dịch vụ.",
					"Hứng khởi" => "Cảm xúc bài thơ này là: Hứng khởi. Hãy làm sao để bài thơ này truyền cảm hứng, sử dụng ngôn từ đầy năng lượng và khích lệ.",
					"Khẩn cấp" => "Cảm xúc bài thơ này là: Khẩn cấp. Hãy làm sao để bài thơ này khơi gợi cảm giác cấp bách, sử dụng ngôn từ thôi thúc hành động ngay.",
					"Hạnh phúc" => "Cảm xúc bài thơ này là: Hạnh phúc. Hãy làm sao để bài thơ này mang cảm giác vui vẻ, nhẹ nhàng, thể hiện sự hài lòng và hân hoan.",
					"Tự hào" => "Cảm xúc bài thơ này là: Tự hào. Hãy làm sao để bài thơ này khơi dậy niềm tự hào, sử dụng ngôn từ tôn vinh giá trị và thành tựu.",
					"Thấu hiểu" => "Cảm xúc bài thơ này là: Thấu hiểu. Hãy làm sao để bài thơ này thể hiện sự đồng cảm với khó khăn hoặc nhu cầu của khách hàng.",
					"Khao khát" => "Cảm xúc bài thơ này là: Khao khát. Hãy làm sao để bài thơ này khơi gợi mong muốn sở hữu, sử dụng ngôn từ làm nổi bật giá trị độc đáo.",
					"An tâm" => "Cảm xúc bài thơ này là: An tâm. Hãy làm sao để bài thơ này làm rõ sự yên tâm, nhấn mạnh tính an toàn và đảm bảo của sản phẩm/dịch vụ.",
					"Truyền cảm hứng" => "Cảm xúc bài thơ này là: Truyền cảm hứng. Hãy làm sao để bài thơ này thể hiện sự lạc quan và động lực để khách hàng cảm thấy được khích lệ.",
					"Tò mò" => "Cảm xúc bài thơ này là: Tò mò. Hãy làm sao để bài thơ này khơi dậy sự tò mò, sử dụng ngôn từ làm người đọc muốn tìm hiểu thêm.",
					"Yêu thương" => "Cảm xúc bài thơ này là: Yêu thương. Hãy làm sao để bài thơ này thể hiện sự quan tâm, tình cảm dành cho bản thân hoặc gia đình.",
					"Hài hước" => "Cảm xúc bài thơ này là: Hài hước. Hãy làm sao để bài thơ này mang phong cách dí dỏm, tạo cảm giác thoải mái và vui vẻ.",
					"Hoài niệm" => "Cảm xúc bài thơ này là: Hoài niệm. Hãy làm sao để bài thơ này gợi nhớ những kỷ niệm đẹp và cảm xúc ấm áp trong quá khứ.",
					"Cảm động" => "Cảm xúc bài thơ này là: Cảm động. Hãy làm sao để bài thơ này mang đến cảm giác xúc động, thể hiện tình cảm sâu sắc và chân thành."
				];
				if (isset($poemEmotionMapping[$data['poem_emotion']])) {
					$parts[] = $poemEmotionMapping[$data['poem_emotion']];
				}
			}

			// Goal
			if (!empty($data['goal'])) {
				$goalMapping = [
					"Bán hàng" => "Mục đích bài thơ này là: Bán hàng. Hãy tạo ra 1 bài thơ để có được mục đích cuối là nhấn mạnh lợi ích và giá trị của sản phẩm/dịch vụ, tạo động lực để khách hàng quyết định mua ngay.",
					"Giới thiệu thương hiệu" => "Mục đích bài thơ này là: Giới thiệu thương hiệu. Hãy tạo ra 1 bài thơ để có được mục đích cuối là làm nổi bật giá trị cốt lõi và hình ảnh thương hiệu, giúp khách hàng ghi nhớ.",
					"Tạo tương tác" => "Mục đích bài thơ này là: Tạo tương tác. Hãy tạo ra 1 bài thơ để có được mục đích cuối là khuyến khích khách hàng tham gia bình luận, chia sẻ hoặc thảo luận về sản phẩm/dịch vụ.",
					"Tăng nhận diện thương hiệu" => "Mục đích bài thơ này là: Tăng nhận diện thương hiệu. Hãy tạo ra 1 bài thơ để có được mục đích cuối là làm nổi bật thương hiệu với phong cách độc đáo, dễ nhớ.",
					"Khuyến khích sử dụng dịch vụ" => "Mục đích bài thơ này là: Khuyến khích sử dụng dịch vụ. Hãy tạo ra 1 bài thơ để có được mục đích cuối là nhấn mạnh sự tiện lợi và lợi ích khi khách hàng chọn dịch vụ này.",
					"Kích thích tò mò" => "Mục đích bài thơ này là: Kích thích tò mò. Hãy tạo ra 1 bài thơ để có được mục đích cuối là khơi dậy sự tò mò của khách hàng, khiến họ muốn tìm hiểu thêm về sản phẩm/dịch vụ.",
					"Chào mừng sự kiện" => "Mục đích bài thơ này là: Chào mừng sự kiện. Hãy tạo ra 1 bài thơ để có được mục đích cuối là để kỷ niệm hoặc quảng bá cho một sự kiện đặc biệt của thương hiệu.",
					"Cảm ơn khách hàng" => "Mục đích bài thơ này là: Cảm ơn khách hàng. Hãy tạo ra 1 bài thơ để có được mục đích cuối là thể hiện lòng biết ơn sâu sắc đối với khách hàng đã đồng hành cùng thương hiệu.",
					"Truyền cảm hứng" => "Mục đích bài thơ này là: Truyền cảm hứng. Hãy tạo ra 1 bài thơ để có được mục đích cuối là mang đến động lực và niềm tin tích cực cho khách hàng.",
					"Giải trí" => "Mục đích bài thơ này là: Giải trí. Hãy tạo ra 1 bài thơ để có được mục đích cuối là với nội dung vui nhộn, nhẹ nhàng, tạo cảm giác thoải mái cho người đọc."
				];
				if (isset($goalMapping[$data['goal']])) {
					$parts[] = $goalMapping[$data['goal']];
				}
			}

			// Key Message
			if (!empty($data['key_message'])) {
				$parts[] = "Thông điệp chính (Key Message) của bài viết sẽ là: " . $data['key_message'];
			}

			// Keywords
			if (!empty($data['keywords'])) {
				$parts[] = "Từ khóa đặc biệt cần có trong bài thơ là: " . $data['keywords'];
			}

			// Trending Context
			if (!empty($data['trending_context'])) {
				$parts[] = "Bài thơ sẽ có 1 bối cảnh (hoặc trending) mà đám đông đang quan tâm là: " . $data['trending_context'];
			}

			return !empty($parts) ? implode(".\n", $parts) . "." : "";
		} catch (\Exception $e) {
			return "Error processing poem content structure";
		}
	}

	private function generateThongTinBoSungNangCao3Prompt(): string
	{
		try {
			$$data = $this->inputData;

			$parts = [];

			// Song Style
			if (!empty($data['song_style'])) {
				$songStyleMapping = [
					"Hài hước" => "Phong cách bài hát này là: Hài hước. Hãy sử dụng ngôn từ trong bài hát mang phong cách hài hước, sử dụng ngôn ngữ dí dỏm và vui nhộn để tạo cảm giác thoải mái và giải trí cho người đọc.",
					"Lãng mạn" => "Phong cách bài hát này là: Lãng mạn. Hãy sử dụng ngôn ngữ trong bài hát mang phong cách lãng mạn, sử dụng từ ngữ bay bổng, nhẹ nhàng, tạo cảm giác tình cảm sâu sắc và tinh tế.",
					"Cảm động" => "Phong cách bài hát này là: Cảm động. Hãy sử dụng ngôn ngữ trong bài hát mang phong cách cảm động, sử dụng ngôn từ xúc động, sâu sắc để chạm đến trái tim người đọc.",
					"Sôi động" => "Phong cách bài hát này là: Sôi động. Hãy sử dụng ngôn từ trong bài hát mang phong cách sôi động, sử dụng ngôn ngữ mạnh mẽ, đầy năng lượng để truyền cảm hứng và động lực.",
					"Cổ điển" => "Phong cách bài hát này là: Cổ điển. Hãy sử dụng ngôn từ trong bài hát mang phong cách cổ điển, sử dụng ngôn từ truyền thống, mượt mà và gợi nhớ phong vị văn hóa xưa.",
					"Hiện đại" => "Phong cách bài hát này là: Hiện đại. Hãy sử dụng ngôn từ trong bài hát mang phong cách hiện đại, sử dụng ngôn ngữ trẻ trung, phá cách và gần gũi với xu hướng ngày nay.",
					"Trang trọng" => "Phong cách bài hát này là: Trang trọng. Hãy sử dụng ngôn từ trong bài hát mang phong cách trang trọng, sử dụng ngôn từ lịch sự, nghiêm túc, phù hợp với bối cảnh chuyên nghiệp.",
					"Thân thiện" => "Phong cách bài hát này là: Thân thiện. Hãy sử dụng ngôn từ trong bài hát mang phong cách thân thiện, sử dụng ngôn ngữ gần gũi, dễ hiểu và tạo cảm giác gần gũi với người đọc.",
					"Tinh nghịch" => "Phong cách bài hát này là: Tinh nghịch. Hãy sử dụng ngôn từ trong bài hát mang phong cách tinh nghịch, sử dụng ngôn ngữ trẻ trung, hài hước và phù hợp với những tình huống vui nhộn.",
					"Giản dị" => "Phong cách bài hát này là: Giản dị. Hãy sử dụng ngôn từ trong bài hát mang phong cách giản dị, sử dụng ngôn ngữ đơn giản, chân thực, phù hợp với đời sống thường ngày."
				];
				if (isset($songStyleMapping[$data['song_style']])) {
					$parts[] = $songStyleMapping[$data['song_style']];
				}
			}

			// Song Emotion
			if (!empty($data['song_emotion'])) {
				$songEmotionMapping = [
					"Tin tưởng" => "Cảm xúc bài hát này là: Tin tưởng. Hãy làm sao để bài hát này mang cảm giác tin cậy, sử dụng ngôn từ thể hiện sự an tâm và uy tín của sản phẩm/dịch vụ.",
					"Hứng khởi" => "Cảm xúc bài hát này là: Hứng khởi. Hãy làm sao để bài hát này truyền cảm hứng, sử dụng ngôn từ đầy năng lượng và khích lệ.",
					"Khẩn cấp" => "Cảm xúc bài hát này là: Khẩn cấp. Hãy làm sao để bài hát này khơi gợi cảm giác cấp bách, sử dụng ngôn từ thôi thúc hành động ngay.",
					"Hạnh phúc" => "Cảm xúc bài hát này là: Hạnh phúc. Hãy làm sao để bài hát này mang cảm giác vui vẻ, nhẹ nhàng, thể hiện sự hài lòng và hân hoan.",
					"Tự hào" => "Cảm xúc bài hát này là: Tự hào. Hãy làm sao để bài hát này khơi dậy niềm tự hào, sử dụng ngôn từ tôn vinh giá trị và thành tựu.",
					"Thấu hiểu" => "Cảm xúc bài hát này là: Thấu hiểu. Hãy làm sao để bài hát này thể hiện sự đồng cảm với khó khăn hoặc nhu cầu của khách hàng.",
					"Khao khát" => "Cảm xúc bài hát này là: Khao khát. Hãy làm sao để bài hát này khơi gợi mong muốn sở hữu, sử dụng ngôn từ làm nổi bật giá trị độc đáo.",
					"An tâm" => "Cảm xúc bài hát này là: An tâm. Hãy làm sao để bài hát này làm rõ sự yên tâm, nhấn mạnh tính an toàn và đảm bảo của sản phẩm/dịch vụ.",
					"Truyền cảm hứng" => "Cảm xúc bài hát này là: Truyền cảm hứng. Hãy làm sao để bài hát này thể hiện sự lạc quan và động lực để khách hàng cảm thấy được khích lệ.",
					"Tò mò" => "Cảm xúc bài hát này là: Tò mò. Hãy làm sao để bài hát này khơi dậy sự tò mò, sử dụng ngôn từ làm người đọc muốn tìm hiểu thêm.",
					"Yêu thương" => "Cảm xúc bài hát này là: Yêu thương. Hãy làm sao để bài hát này thể hiện sự quan tâm, tình cảm dành cho bản thân hoặc gia đình.",
					"Hài hước" => "Cảm xúc bài hát này là: Hài hước. Hãy làm sao để bài hát này mang phong cách dí dỏm, tạo cảm giác thoải mái và vui vẻ.",
					"Hoài niệm" => "Cảm xúc bài hát này là: Hoài niệm. Hãy làm sao để bài hát này gợi nhớ những kỷ niệm đẹp và cảm xúc ấm áp trong quá khứ.",
					"Cảm động" => "Cảm xúc bài hát này là: Cảm động. Hãy làm sao để bài hát này mang đến cảm giác xúc động, thể hiện tình cảm sâu sắc và chân thành."
				];
				if (isset($songEmotionMapping[$data['song_emotion']])) {
					$parts[] = $songEmotionMapping[$data['song_emotion']];
				}
			}

			// Goal
			if (!empty($data['goal'])) {
				$goalMapping = [
					"Bán hàng" => "Mục đích bài hát này là: Bán hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh lợi ích và giá trị của sản phẩm/dịch vụ, tạo động lực để khách hàng quyết định mua ngay.",
					"Giới thiệu thương hiệu" => "Mục đích bài hát này là: Giới thiệu thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật giá trị cốt lõi và hình ảnh thương hiệu, giúp khách hàng ghi nhớ.",
					"Tạo tương tác" => "Mục đích bài hát này là: Tạo tương tác. Hãy tạo ra 1 bài hát để có được mục đích cuối là khuyến khích khách hàng tham gia bình luận, chia sẻ hoặc thảo luận về sản phẩm/dịch vụ.",
					"Tăng nhận diện thương hiệu" => "Mục đích bài hát này là: Tăng nhận diện thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật thương hiệu với phong cách độc đáo, dễ nhớ.",
					"Khuyến khích sử dụng dịch vụ" => "Mục đích bài hát này là: Khuyến khích sử dụng dịch vụ. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh sự tiện lợi và lợi ích khi khách hàng chọn dịch vụ này.",
					"Kích thích tò mò" => "Mục đích bài hát này là: Kích thích tò mò. Hãy tạo ra 1 bài hát để có được mục đích cuối là khơi dậy sự tò mò của khách hàng, khiến họ muốn tìm hiểu thêm về sản phẩm/dịch vụ.",
					"Chào mừng sự kiện" => "Mục đích bài hát này là: Chào mừng sự kiện. Hãy tạo ra 1 bài hát để có được mục đích cuối là để kỷ niệm hoặc quảng bá cho một sự kiện đặc biệt của thương hiệu.",
					"Cảm ơn khách hàng" => "Mục đích bài hát này là: Cảm ơn khách hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là thể hiện lòng biết ơn sâu sắc đối với khách hàng đã đồng hành cùng thương hiệu.",
					"Truyền cảm hứng" => "Mục đích bài hát này là: Truyền cảm hứng. Hãy tạo ra 1 bài hát để có được mục đích cuối là mang đến động lực và niềm tin tích cực cho khách hàng.",
					"Giải trí" => "Mục đích bài hát này là: Giải trí. Hãy tạo ra 1 bài hát để có được mục đích cuối là với nội dung vui nhộn, nhẹ nhàng, tạo cảm giác thoải mái cho người đọc."
				];
				if (isset($goalMapping[$data['goal']])) {
					$parts[] = $goalMapping[$data['goal']];
				}
			}

			// Key Message
			if (!empty($data['key_message'])) {
				$parts[] = "Thông điệp chính (Key Message) của bài viết sẽ là: " . $data['key_message'];
			}

			// Keywords
			if (!empty($data['keywords'])) {
				$parts[] = "Từ khóa đặc biệt cần có trong bài hát là: " . $data['keywords'];
			}

			// Trending Context
			if (!empty($data['trending_context'])) {
				$parts[] = "Bài hát sẽ có 1 bối cảnh (hoặc trending) mà đám đông đang quan tâm là: " . $data['trending_context'];
			}

			return !empty($parts) ? implode(".\n", $parts) . "." : "";
		} catch (\Exception $e) {
			return "Error processing song content structure";
		}
	}

	private function generateThongTinBoSungNangCao4Prompt(): string
	{
		try {
			$data = $this->inputData;
			$mucTieu = $data['muc_tieu'] ?? '';

			$mucTieuMapping = [
				"Quảng Bá Sản Phẩm/Dịch Vụ" => "Hãy tạo một bài viết quảng bá [tên sản phẩm/dịch vụ] theo cấu trúc sau: Mở đầu bằng một câu chuyện ngắn về vấn đề điển hình mà khách hàng thường gặp Giới thiệu giải pháp độc đáo của sản phẩm/dịch vụ, kèm dữ liệu cụ thể về hiệu quả Mô tả chi tiết 3 tính năng nổi bật nhất, kết hợp với trải nghiệm thực tế của người dùng Kết thúc với một viễn cảnh tích cực khi sử dụng sản phẩm/dịch vụ Giọng điệu: Chân thực, đồng cảm, không quá quảng cáo Độ dài: 500-700 từ",
				"Tăng Nhận Thức Thương Hiệu" => "Viết một câu chuyện thương hiệu theo format sau: Bắt đầu với thời điểm 'eureka' - khoảnh khắc khởi nguồn của thương hiệu Chia sẻ 3 thách thức lớn nhất và cách vượt qua Mô tả chi tiết quá trình phát triển sản phẩm đầu tiên Giới thiệu những con người đằng sau thương hiệu Kết nối giá trị cốt lõi với tầm nhìn tương lai Yêu cầu: Sử dụng ngôn ngữ kể chuyện sống động Thêm các chi tiết cụ thể về địa điểm, thời gian Kết hợp trích dẫn từ người sáng lập/nhân viên Độ dài: 800-1000 từ",
				"Thúc Đẩy Tương Tác Khách Hàng" => "Tạo một bài đăng tương tác với cấu trúc: Mở đầu gây chú ý: Chia sẻ một góc nhìn thú vị về ngành Nội dung chính: Đặt 3-4 câu hỏi mở có tính kích thích tư duy Thêm 1-2 tình huống giả định để người đọc suy ngẫm Chia sẻ quan điểm cá nhân để kích thích thảo luận Kêu gọi hành động: Tạo mini-contest với phần thưởng hấp dẫn Khuyến khích chia sẻ trải nghiệm cá nhân Giọng điệu: Thân thiện, tò mò, khám phá Độ dài: 300-400 từ",
				"Tạo Lead và Chuyển Đổi" => "Thiết kế bài viết lead magnet theo mô hình AIDA: Attention (Gây sự chú ý): Mở đầu với số liệu thống kê gây sốc về vấn đề trong ngành Đặt câu hỏi khiến độc giả tự nhận diện điểm yếu Interest (Tạo sự quan tâm): Giới thiệu giải pháp độc đáo Thêm bằng chứng xã hội từ 2-3 khách hàng tiêu biểu Desire (Khơi gợi mong muốn): Mô tả chi tiết ưu đãi với giá trị cụ thể Tạo cảm giác khan hiếm/cấp bách hợp lý Action (Kêu gọi hành động): Kêu gọi hành động rõ ràng với form đăng ký đơn giản Cam kết/Đảm bảo hoàn tiền để tăng sự tin tưởng Độ dài: 600-800 từ",
				"Tạo Dẫn Đường Cho Trang Đích" => "Tạo một bài viết cuốn hút, giới thiệu sản phẩm/dịch vụ và đặt một liên kết tới trang đích (landing page) để khách hàng tìm hiểu thêm. Kèm theo lời kêu gọi hành động (CTA) mạnh mẽ như 'Khám phá ngay hôm nay!'",
				"Xây Dựng và Nuôi Dưỡng Mối Quan Hệ Khách Hàng" => "Tạo bài viết tri ân khách hàng theo framework: Mở đầu: Nhấn mạnh một khoảnh khắc đặc biệt với khách hàng Chia sẻ một số liệu ấn tượng về cộng đồng khách hàng Nội dung chính: Liệt kê 3 cải tiến lớn dựa trên phản hồi của khách hàng Chia sẻ hậu trường về quá trình thực hiện Kể 2-3 câu chuyện thành công của khách hàng Tầm nhìn tương lai: Hé lộ những cập nhật sắp tới Mô tả cách khách hàng có thể tham gia phát triển Giọng điệu: Chân thành, biết ơn, hướng đến tương lai Định dạng: Kết hợp văn bản + hình ảnh dòng thời gian Độ dài: 700-900 từ",
				"Tối Ưu Hóa Cho SEO" => "Tạo bài viết tối ưu SEO với cấu trúc: Tiêu đề: Sử dụng công thức Vấn đề - Giải pháp - Lợi ích Mô tả meta: Tối đa 155 ký tự, chứa từ khóa chính H1: Chứa từ khóa chính một cách tự nhiên Mở đầu: Câu mở đầu gây tò mò Định nghĩa vấn đề và cam kết giá trị Nội dung chính: Chia thành 3-4 H2 theo mục đích tìm kiếm Mỗi phần trả lời 1 câu hỏi cụ thể Thêm phần Câu hỏi thường gặp Kết luận: Tóm tắt các điểm chính Gợi ý bước tiếp theo Mật độ từ khóa: 1-2% Liên kết nội bộ/ngoài: 2-3 mỗi loại Độ dài: 1500-2000 từ",
				"Giáo Dục Khách Hàng" => "Tạo chuỗi bài viết giáo dục theo mô hình: Phần cơ bản: Đặt vấn đề rõ ràng Kiến thức nền cần thiết Giải thích các khái niệm cốt lõi một cách đơn giản Hướng dẫn thực hành: Hướng dẫn từng bước với ảnh chụp màn hình Những lỗi thường gặp và cách tránh Mẹo hay từ người dùng chuyên nghiệp Kỹ thuật nâng cao: Các trường hợp sử dụng nâng cao Chiến lược tối ưu hóa Hướng dẫn khắc phục sự cố Yêu cầu: Sử dụng phép so sánh dễ hiểu Thêm ví dụ thực tế Có yếu tố tương tác (câu hỏi trắc nghiệm, checklist) Độ dài mỗi phần: 1000-1200 từ",
				"Hỗ Trợ Chiến Dịch Marketing" => "Tạo một bài viết theo cấu trúc: Giai đoạn trước khi ra mắt: Nội dung 'nhá hàng' gợi tò mò Phân tích xu hướng ngành Phỏng vấn/Tóm tắt ý kiến chuyên gia Giai đoạn ra mắt: Thông báo ra mắt theo lối kể chuyện Nổi bật các tính năng với trường hợp sử dụng Câu chuyện thành công của khách hàng Giai đoạn sau ra mắt: Trình bày nội dung do người dùng tạo Chỉ số hiệu suất & các cột mốc quan trọng Hoạt động tương tác cộng đồng Yêu cầu về định dạng: Hình ảnh thương hiệu nhất quán Điều chỉnh phù hợp với nhiều nền tảng Hashtag chiến dịch rõ ràng Kết hợp nội dung: 40% giáo dục, 30% quảng cáo, 30% tương tác Thời gian: 2-3 tuần mỗi giai đoạn",
				"Xây Dựng Cộng Đồng" => "Tạo một bài viết tương tác cộng đồng: Chuỗi chào mừng: Mục đích & giá trị của cộng đồng Trình bày lợi ích của thành viên Hướng dẫn bắt đầu Các nội dung chính thường xuyên: Chủ đề thảo luận hàng tuần Giao lưu hỏi đáp với chuyên gia hàng tháng Chia sẻ câu chuyện thành công Chủ đề chia sẻ tài nguyên Cơ chế tương tác: Hệ thống điểm & phần thưởng Chương trình ghi nhận thành viên Dự án hợp tác Gặp gỡ trực tuyến/ngoại tuyến Nguyên tắc nội dung: Tập trung vào việc tạo giá trị Khuyến khích hỗ trợ lẫn nhau Chúc mừng thành công của thành viên Phong cách điều hành: Hỗ trợ nhưng có cấu trúc",
				"Thúc Đẩy Bán Hàng và Tạo Doanh Thu" => "Tạo một bài viết kích hoạt bán hàng với cấu trúc: Chuẩn bị trước khi bán: Chuỗi nhắc nhở về điểm yếu Thu thập bằng chứng xã hội Trình bày giá trị cốt lõi Nội dung ra mắt: Tuần tự Vấn đề - Kích động - Giải pháp Chi tiết ưu đãi giới hạn thời gian Tiết lộ gói quà tặng kèm Dự đoán Câu hỏi thường gặp Tạo cảm giác cấp bách: Yếu tố đếm ngược Chỉ báo khan hiếm Khuếch đại FOMO (Fear Of Missing Out - Nỗi sợ bỏ lỡ) Cảnh báo tăng giá Chuỗi chốt sale: Nhắc nhở cơ hội cuối cùng Tóm tắt lại lợi ích cuối cùng Hướng dẫn xác nhận hành động Định dạng: Đa kênh với chuỗi email là chính Giọng điệu: Cấp bách nhưng không gây áp lực Thời gian: 5-7 ngày",
				"Phản Hồi và Giải Quyết Vấn Đề của Khách Hàng" => "Tạo một bài viết phản hồi khách hàng: Tin nhắn xác nhận: Lời cảm ơn cá nhân hóa Đồng cảm với điểm yếu Cam kết thời gian xử lý Trình bày giải quyết vấn đề: Phân tích chi tiết vấn đề Quy trình phát triển giải pháp Thời gian triển khai So sánh trước/sau Minh chứng cải tiến: Những thay đổi cụ thể đã thực hiện Chỉ số tác động Nâng cao trải nghiệm người dùng Xem trước lộ trình tương lai Định dạng: Nhiều hình ảnh với infographics Giọng điệu: Chuyên nghiệp nhưng đồng cảm Theo dõi: Lên lịch các điểm kiểm tra Độ dài: 600-800 từ",
				"Phá vỡ rào cản khách hàng" => "Tạo một bài viết xử lý phản đối: Những trở ngại thường gặp: Liệt kê 5 điểm do dự hàng đầu của khách hàng Phân tích nguyên nhân gốc rễ cho mỗi vấn đề Lập luận phản bác dựa trên dữ liệu Framework giải pháp: Quy trình giải quyết từng bước Các lựa chọn thay thế khả dụng Truy cập tài nguyên hỗ trợ Nghiên cứu điển hình thành công Yếu tố xây dựng niềm tin: Chi tiết đảm bảo hoàn tiền Phạm vi hỗ trợ kỹ thuật Chính sách bảo vệ khách hàng Xác nhận từ bên thứ ba Định dạng: Hỏi & Đáp với hình ảnh minh họa Giọng điệu: Trấn an và tập trung vào giải pháp Độ dài: 1000-1200 từ",
				"Tạo Nội Dung Viral" => "Tạo một bài viết nội dung viral: Yếu tố gây chú ý: Lựa chọn yếu tố kích thích cảm xúc Kỹ thuật phá vỡ khuôn mẫu Tạo ra khoảng trống tò mò Cấu trúc nội dung: Phát triển mạch truyện Đặt điểm nút thắt Sự hài lòng với giải pháp Yếu tố thúc đẩy chia sẻ Cơ chế lan truyền: Tối ưu hóa cho từng nền tảng cụ thể Chiến lược hashtag Kế hoạch hợp tác với người có ảnh hưởng Khuyến khích chia sẻ Kế hoạch phân phối: Chiến lược gieo mầm Thời điểm khuếch đại Điều chỉnh phù hợp với nhiều nền tảng Theo dõi tương tác Định dạng: Nội dung ngắn với hình ảnh mạnh mẽ Cảm xúc đỉnh điểm: Vui vẻ/Ngạc nhiên/Kính sợ Thời lượng: 30-60 giây",
				"Phát Triển Thương Hiệu Cá Nhân" => "Tạo câu chuyện thương hiệu cá nhân: Câu chuyện nguồn gốc: Mô tả khoảnh khắc quan trọng Những thử thách ban đầu gặp phải Bài học kinh nghiệm rút ra Phát triển tầm nhìn Hành trình phát triển: Dòng thời gian các cột mốc quan trọng Bài học từ những thất bại Ảnh hưởng từ người cố vấn Hình thành triết lý Trình bày chuyên môn: Lập bản đồ lĩnh vực kiến thức Nổi bật những thành tựu Đóng góp cho ngành Khát vọng tương lai Phong cách: Kể chuyện chân thực Giọng điệu: Cá nhân nhưng chuyên nghiệp Hình ảnh: Định dạng dòng thời gian hành trình Độ dài: 800-1000 từ",
				"Tạo Uy Tín và Chứng Minh Chuyên Môn" => "Tạo một bài viết phát triển nội dung tư tưởng lãnh đạo: Bài viết chuyên sâu về ngành: Phân tích xu hướng (3-5 năm) Kết quả nghiên cứu thị trường Dự đoán của chuyên gia Cơ hội đổi mới Cấu trúc nghiên cứu điển hình: Đặt bối cảnh vấn đề Quy trình phát triển giải pháp Thử thách triển khai Kết quả & chỉ số đo lường Bài học kinh nghiệm chính Minh chứng chuyên môn: Giải thích phương pháp luận Hướng dẫn thực hành tốt nhất Cảnh báo về những lỗi thường gặp Chiến lược nâng cao Định dạng: Nội dung dài với hình ảnh trực quan dữ liệu Nghiên cứu: Bao gồm những phát hiện ban đầu Độ sâu: Chuyên môn nhưng dễ tiếp cận Độ dài: 1500-2000 từ",
				"Tối Ưu Hóa Trải Nghiệm Khách Hàng" => "Tạo một bài viết nâng cao trải nghiệm khách hàng: Nổi bật sự đổi mới: Lợi ích của tính năng mới Kết nối vấn đề - giải pháp Cải thiện trải nghiệm người dùng Tiến bộ kỹ thuật Chi tiết triển khai: Thời gian triển khai Hướng dẫn truy cập Tài nguyên đào tạo Kênh hỗ trợ Đo lường tác động: Chỉ số hiệu suất Tích hợp phản hồi của khách hàng Chỉ báo sự hài lòng Minh chứng ROI (Return On Investment - Lợi tức đầu tư) Định dạng: Bài thuyết trình đa phương tiện Phong cách: Rõ ràng, hướng dẫn Tập trung: Lợi ích của người dùng là trên hết Độ dài: 700-900 từ",
				"Thúc Đẩy Văn Hóa Doanh Nghiệp" => "Tạo một bài viết giới thiệu văn hóa doanh nghiệp: Thể hiện giá trị: Nguyên tắc cốt lõi trong hành động Câu chuyện của thành viên trong nhóm Ví dụ về thực tiễn hàng ngày Tác động đến chất lượng công việc Hậu trường: Khoảnh khắc cuộc sống văn phòng Sự hợp tác trong nhóm Quy trình đổi mới Sự kiện kỷ niệm Tập trung vào con người: Nổi bật nhân viên Câu chuyện trưởng thành Tôn vinh sự đa dạng Tham gia cộng đồng Định dạng: Kể chuyện với phương tiện truyền thông phong phú Giọng điệu: Chân thực & ấm áp Phong cách: Tiếp cận theo lối phim tài liệu Độ dài: 800-1000 từ",
				"Tác Động Xã Hội và Trách Nhiệm Cộng Đồng" => "Tạo câu chuyện về tác động xã hội: Tổng quan về sáng kiến: Đặt vấn đề Phương pháp giải quyết Cam kết nguồn lực Sự tham gia của cộng đồng Đo lường tác động: Kết quả định lượng Kết quả định tính Câu chuyện của người thụ hưởng Tác động dài hạn Tập trung vào tính bền vững: Cân nhắc về môi trường Trách nhiệm xã hội Khả năng kinh tế Cam kết tương lai Định dạng: Theo kiểu báo cáo tác động Giọng điệu: Thực tế nhưng giàu cảm xúc Hình ảnh: Trực quan hóa dữ liệu + câu chuyện con người Độ dài: 1200-1500 từ"
			];

			return isset($mucTieuMapping[$mucTieu]) ? $mucTieuMapping[$mucTieu] : "";
		} catch (\Exception $e) {
			return "Error processing campaign content structure";
		}
	}

	private function processBuoc5(): string
	{
		['prompt' => $prompt, 'text' => $text] = $this->buoc5GeneratePrompt();

		$systemPrompt = $this->config['step5_prompts'][$this->metaData['the_loai']]['system_prompt'];
		$systemPrompt = str_replace([
			'{prompt}',
			'{text}'
		], [
			$prompt,
			$text
		], $systemPrompt);

		return $this->gptClient->sendRequest($systemPrompt, '');
	}

	private function buoc5GeneratePrompt(): array
	{
		return match ($this->metaData['the_loai']) {
			'Bài viết Quảng cáo sản phẩm' => $this->buoc5GenerateBaiVietQuangCaoPrompt(),
			'Thơ Quảng cáo sản phẩm' => $this->buoc5GenerateThoQuangCaoPrompt(),
			'Nhạc Doanh nghiệp' => $this->buoc5GenerateNhacQuangCaoPrompt(),
			'Lời nhạc Quảng cáo sản phẩm' => $this->buoc5GenerateNhacQuangCaoPrompt(),
			'Chiến dịch Truyền thông' => $this->buoc5GenerateChienDichTruyenThongPrompt(),
		};
	}

	private function buoc5GenerateBaiVietQuangCaoPrompt(): array
	{
		try {
			$data = $this->inputData;
			$mucTieu = $data['muc_tieu'] ?? '';
			$camXuc = $data['cam_xuc'] ?? '';
			$phongCach = $data['phong_cach'] ?? '';
			$dinhDang = $data['dinh_dang'] ?? '';
			$text = $data['text'] ?? '';

			$promptsMucTieu = [
				"Tăng Nhận Diện Thương Hiệu" => "Tạo một status nói về phần \"Câu chuyện về Thương hiệu\" và nói về phần \"Ưu điểm và giá trị độc đáo (USP)\" với những tính năng độc đáo của Sản phẩm/dịch vụ này, nhấn mạnh sự khác biệt và ưu điểm nổi bật.",
				"Thông Tin Sản Phẩm/Dịch Vụ" => "Tạo một status về tính năng của Sản phẩm/dịch vụ này, hãy nói tới phần \"Tính năng và giải quyết vấn đề\", và nói tới phần \"Ưu điểm và giá trị độc đáo (USP) \" và làm nổi bật phần \"lợi ích cho khách hàng\" và cách nó giải quyết vấn đề cụ thể cho khách hàng.",
				"Tạo trao đổi về Sản Phẩm / Dịch vụ" => "Viết một status kêu gọi cộng đồng thảo luận về kinh nghiệm sử dụng Sản phẩm /Dịch vụ này, khuyến khích khách hàng chia sẻ phản hồi và góp ý về [sản phẩm/dịch vụ], thể hiện sự lắng nghe và cải tiến liên tục. Ví dụ như 'Bạn yêu thích điều gì nhất về [sản phẩm/dịch vụ]?'",
				"Kích Thích Sự Tương Tác (Engagement)" => "Tạo ra 1 bài viết có mục tiêu nhiều tương tác nhất: Hãy tự tìm ra phương thức nào đó, 1 chủ đề nào đó tạo ra động lực tương tác với bài viết một cách tự nhiên nhất.",
				"Quảng bá về Tiện ích và Thái độ phục vụ" => "Tạo một bài viết thông báo với thông tin liên quan tới phần \"Dịch vụ phục vụ hỗ trợ\", nói tới thái độ chăm sóc khách hàng, tiện ích phục vụ của chúng tôi, Nói tới \"Kênh hỗ trợ khách hàng\", hệ thống giao hàng và phân phối của chúng tôi.",
				"Tạo Dựng Mối Quan Hệ Với Khách Hàng" => "Tạo một bài viết chia sẻ các phản hội của khách hàng theo phần \"Phản hồi đánh giá của khách hàng\", kể câu chuyện hoặc kinh nghiệm của khách hàng sử dụng [sản phẩm/dịch vụ], nhấn mạnh mối quan hệ và sự tin tưởng giữa khách hàng và thương hiệu.",
				"Xu Hướng Và Tương Lai" => "Viết một status kết nối [sản phẩm/dịch vụ] với xu hướng hiện tại hoặc sự kiện phổ biến, để thể hiện sự liên quan và hiện đại của thương hiệu. Cùng với đó, thể hiện tầm nhìn của Doanh Nghiệp và sự hữu ích của sản phẩm. Nói về nội dung trong phần \"Xu hướng và Mục tiêu thương hiệu\"",
				"Tăng Cường Hình Ảnh Thương Hiệu" => "Tạo một status thể hiện giá trị và văn hóa của thương hiệu qua [sản phẩm/dịch vụ], như cam kết về chất lượng, trách nhiệm xã hội, hoặc nội dung trong phần \"Xu hướng và Mục tiêu thương hiệu\" với sự đổi mới sáng tạo của công ty. Nói về phần \"Chứng nhận và thông tin tin cậy\""
			];

			$promptsCamXuc = [
				"Tin tưởng" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tin tưởng. Hãy viết bài quảng cáo tập trung vào việc xây dựng niềm tin, nhấn mạnh chứng nhận, đánh giá từ khách hàng và uy tín của sản phẩm.",
				"Hứng khởi" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hứng khởi. Hãy tạo bài viết làm nổi bật sự mới lạ, độc đáo của sản phẩm, khiến khách hàng cảm thấy hứng khởi và muốn khám phá ngay.",
				"Khẩn cấp" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khẩn cấp. Viết bài kèm thông điệp khẩn cấp, sử dụng ngôn ngữ nhấn mạnh ưu đãi có thời hạn hoặc số lượng giới hạn để thúc đẩy khách hàng hành động.",
				"Hạnh phúc" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là niềm hạnh phúc. Hãy viết bài mô tả cảm giác hạnh phúc và niềm vui mà khách hàng có thể trải nghiệm khi sử dụng sản phẩm/dịch vụ.",
				"Tự hào" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tự hào. Tạo bài viết nhấn mạnh giá trị đẳng cấp và lý do khách hàng sẽ cảm thấy tự hào khi sử dụng sản phẩm.",
				"Thấu hiểu" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự thấu hiểu. Hãy tạo bài viết thể hiện sự đồng cảm với khó khăn của khách hàng và cách sản phẩm/dịch vụ giúp họ giải quyết vấn đề.",
				"Khao khát" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khao khát. Viết bài tập trung vào việc khơi gợi khao khát của khách hàng bằng cách nhấn mạnh giá trị và lợi ích độc đáo của sản phẩm.",
				"An tâm" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự an tâm. Tạo nội dung làm rõ các cam kết, bảo hành, và chứng minh tính an toàn, hiệu quả của sản phẩm.",
				"Truyền cảm hứng" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự cảm hứng. Hãy viết bài mang tính truyền cảm hứng, nhấn mạnh những thay đổi tích cực mà sản phẩm/dịch vụ có thể mang lại.",
				"Tò mò" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tò mò. Viết bài mở đầu với một câu chuyện hoặc dữ liệu thú vị, làm khách hàng tò mò và muốn tìm hiểu chi tiết hơn.",
				"Yêu thương" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự yêu thương. Hãy tạo bài viết làm nổi bật cách sản phẩm/dịch vụ mang lại sự yêu thương, chăm sóc cho bản thân hoặc gia đình.",
				"Vui vẻ Hài hước" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hài hước. Hãy viết bài quảng cáo hài hước và dí dỏm để tạo sự thích thú và dễ dàng chia sẻ trên mạng xã hội.",
				"Động lực" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự động lực. Hãy viết bài quảng cáo mang tính khích lệ, tập trung vào việc thúc đẩy khách hàng thực hiện hành động cụ thể ngay lập tức.",
				"Hoài niệm" => "Hãy viết lại bài viết với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hoài niệm. Viết bài quảng cáo gợi nhớ những kỷ niệm đẹp trong quá khứ và kết nối cảm xúc với sản phẩm/dịch vụ."
			];

			$promptsPhongCach = [
				"Trang Trọng" => "Hãy viết lại bài viết với Phong cách viết: Tạo ra một phong cách trang trọng cho nội dung, phù hợp với môi trường kinh doanh chính thức hoặc các sự kiện quan trọng.",
				"Hấp Dẫn và Sáng Tạo" => "Hãy viết lại bài viết với Phong cách viết: Làm cho nội dung trở nên sáng tạo và độc đáo, thu hút sự chú ý bằng cách thể hiện một cách tiếp cận mới lạ.",
				"Thư Giãn và Thân Thiện" => "Hãy viết lại bài viết với Phong cách viết: Tạo nội dung để nó trở nên thân thiện và dễ chịu, tạo môi trường thư giãn cho người đọc.",
				"Chuyên Nghiệp" => "Hãy viết lại bài viết với Phong cách viết: Tăng cường tính chuyên nghiệp và uy tín của nội dung, sử dụng ngôn ngữ chính xác và thông tin đáng tin cậy.",
				"Hài hước và vui vẻ" => "Hãy viết lại bài viết với Phong cách viết: Thêm yếu tố hài hước và niềm vui vào nội dung, làm cho nó trở nên sinh động và giải trí.",
				"Thể Thao và Năng Động" => "Hãy viết lại bài viết với Phong cách viết: Phản ánh tính năng động và sôi động, thích hợp với những sản phẩm liên quan đến thể thao hoặc hoạt động ngoài trời.",
				"Hướng Dẫn và Giảng Dạy" => "Hãy viết lại bài viết với Phong cách viết: Đưa nội dung thành một hướng dẫn hoặc bài giảng, cung cấp thông tin hữu ích và kiến thức cho người đọc.",
				"Chất Lượng và Tinh Tế" => "Hãy viết lại bài viết với Phong cách viết: Tăng cường chất lượng và tinh tế trong nội dung, nhấn mạnh sự chăm chút và độ chính xác của thông tin.",
				"Ngắn Gọn và Súc Tích" => "Hãy viết lại bài viết với Phong cách viết: Làm cho nội dung trở nên ngắn gọn và đến trực tiếp điểm, loại bỏ mọi thông tin thừa.",
				"Chia Sẻ Kinh Nghiệm Cá Nhân" => "Hãy viết lại bài viết với Phong cách viết: Thêm vào nội dung những trải nghiệm cá nhân hoặc câu chuyện từ người viết, làm cho nó trở nên thực tế và có liên quan hơn.",
				"Truyện Cười và Giải Trí" => "Hãy viết lại bài viết với Phong cách viết: Biến nội dung thành một phần giải trí, thêm truyện cười hoặc yếu tố giải trí để thu hút người đọc.",
				"Đánh Giá và So Sánh" => "Hãy viết lại bài viết với Phong cách viết: Thêm phần đánh giá hoặc so sánh sản phẩm/dịch vụ với các lựa chọn khác, cung cấp thông tin chi tiết và khách quan.",
				"Cảm Xúc và Sâu Sắc" => "Hãy viết lại bài viết với Phong cách viết: Tạo nội dung với cảm xúc sâu sắc, sử dụng ngôn từ mô tả cảm xúc và trải nghiệm đa chiều.",
				"Tone Nghiêm túc" => "Hãy viết lại bài viết với Phong cách viết: Nội dung nghiêm túc và uy tín, thích hợp cho các thông điệp quan trọng.",
				"Tone Lạc quan" => "Hãy viết lại bài viết với Phong cách viết: Làm cho nội dung trở nên lạc quan và tích cực hơn, truyền đạt sự tự tin và khả năng của sản phẩm.",
				"Tone Hấp Dẫn và Thú vị" => "Hãy viết lại bài viết với Phong cách viết: Nội dung hấp dẫn và thú vị, thu hút sự chú ý của người đọc.",
				"Tone Thư Thái, nhẹ nhàng" => "Hãy viết lại bài viết với Phong cách viết: Nội dung tạo cảm giác thư thái và nhẹ nhàng, giúp người đọc cảm thấy thoải mái khi tiếp nhận thông tin."
			];

			$promptsDinhDang = [
				"Tiêu đề hấp dẫn hơn" => "Làm lại bài quảng cáo này, giữ nguyên nội dung và cách làm, nhưng thay đổi cho tiêu đề hấp dẫn hơn, tạo sự tò mò hơn.",
				"Làm lại 1 bài ngắn hơn!" => "Làm lại bài quảng cáo này, giữ nguyên nội dung và cách làm, nhưng làm ngắn gọn hơn.",
				"Làm lại 1 bài dài hơn!" => "Làm lại bài quảng cáo này, giữ nguyên nội dung và cách làm, nhưng làm dài và chi tiết hơn."
			];

			$prompts = [];
			if (!empty($mucTieu) && isset($promptsMucTieu[$mucTieu])) {
				$prompts[] = $promptsMucTieu[$mucTieu];
			}
			if (!empty($camXuc) && isset($promptsCamXuc[$camXuc])) {
				$prompts[] = $promptsCamXuc[$camXuc];
			}
			if (!empty($phongCach) && isset($promptsPhongCach[$phongCach])) {
				$prompts[] = $promptsPhongCach[$phongCach];
			}
			if (!empty($dinhDang) && isset($promptsDinhDang[$dinhDang])) {
				$prompts[] = $promptsDinhDang[$dinhDang];
			}

			$prompt = !empty($prompts) ? implode(" ", $prompts) : "Không tìm thấy tiêu chí phù hợp";

			return [
				"prompt" => $prompt,
				"text" => $text
			];
		} catch (\Exception $e) {
			return [
				"prompt" => "Error: Invalid input data",
				"text" => ""
			];
		}
	}

	private function buoc5GenerateThoQuangCaoPrompt(): array
	{
		try {
			$data = json_decode($this->input, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				return [
					"prompt" => "Error: Invalid JSON input",
					"text" => ""
				];
			}

			$mucTieu = $data['muc_tieu'] ?? '';
			$camXuc = $data['cam_xuc'] ?? '';
			$phongCach = $data['phong_cach'] ?? '';
			$dinhDang = $data['dinh_dang'] ?? '';
			$tuKhoa = $data['keyword'] ?? '';
			$text = $data['text'] ?? '';

			$promptsMucTieu = [
				"Bán hàng" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Bán hàng. Hãy tạo ra 1 bài thơ để có được mục đích cuối là nhấn mạnh lợi ích và giá trị của sản phẩm/dịch vụ, tạo động lực để khách hàng quyết định mua ngay.",
				"Giới thiệu thương hiệu" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Giới thiệu thương hiệu. Hãy tạo ra 1 bài thơ để có được mục đích cuối là làm nổi bật giá trị cốt lõi và hình ảnh thương hiệu, giúp khách hàng ghi nhớ.",
				"Tạo tương tác" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Tạo tương tác. Hãy tạo ra 1 bài thơ để có được mục đích cuối là khuyến khích khách hàng tham gia bình luận, chia sẻ hoặc thảo luận về sản phẩm/dịch vụ.",
				"Tăng nhận diện thương hiệu" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Tăng nhận diện thương hiệu. Hãy tạo ra 1 bài thơ để có được mục đích cuối là làm nổi bật thương hiệu với phong cách độc đáo, dễ nhớ.",
				"Khuyến khích sử dụng dịch vụ" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Khuyến khích sử dụng dịch vụ. Hãy tạo ra 1 bài thơ để có được mục đích cuối là nhấn mạnh sự tiện lợi và lợi ích khi khách hàng chọn dịch vụ này.",
				"Kích thích tò mò" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Kích thích tò mò. Hãy tạo ra 1 bài thơ để có được mục đích cuối là khơi dậy sự tò mò của khách hàng, khiến họ muốn tìm hiểu thêm về sản phẩm/dịch vụ.",
				"Chào mừng sự kiện" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Chào mừng sự kiện. Hãy tạo ra 1 bài thơ để có được mục đích cuối là để kỷ niệm hoặc quảng bá cho một sự kiện đặc biệt của thương hiệu.",
				"Cảm ơn khách hàng" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Cảm ơn khách hàng. Hãy tạo ra 1 bài thơ để có được mục đích cuối là thể hiện lòng biết ơn sâu sắc đối với khách hàng đã đồng hành cùng thương hiệu.",
				"Truyền cảm hứng" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Truyền cảm hứng. Hãy tạo ra 1 bài thơ để có được mục đích cuối là mang đến động lực và niềm tin tích cực cho khách hàng.",
				"Giải trí" => "Hãy viết lại bài thơ này với Mục đích bài thơ này là: Giải trí. Hãy tạo ra 1 bài thơ để có được mục đích cuối là với nội dung vui nhộn, nhẹ nhàng, tạo cảm giác thoải mái cho người đọc."
			];

			$promptsCamXuc = [
				"Tin tưởng" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Tin tưởng. Hãy làm sao để bài thơ này mang cảm giác tin cậy, sử dụng ngôn từ thể hiện sự an tâm và uy tín của sản phẩm/dịch vụ.",
				"Hứng khởi" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Hứng khởi. Hãy làm sao để bài thơ này truyền cảm hứng, sử dụng ngôn từ đầy năng lượng và khích lệ.",
				"Khẩn cấp" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Khẩn cấp. Hãy làm sao để bài thơ này khơi gợi cảm giác cấp bách, sử dụng ngôn từ thôi thúc hành động ngay.",
				"Hạnh phúc" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Hạnh phúc. Hãy làm sao để bài thơ này mang cảm giác vui vẻ, nhẹ nhàng, thể hiện sự hài lòng và hân hoan.",
				"Tự hào" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Tự hào. Hãy làm sao để bài thơ này khơi dậy niềm tự hào, sử dụng ngôn từ tôn vinh giá trị và thành tựu.",
				"Thấu hiểu" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Thấu hiểu. Hãy làm sao để bài thơ này thể hiện sự đồng cảm với khó khăn hoặc nhu cầu của khách hàng.",
				"Khao khát" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Khao khát. Hãy làm sao để bài thơ này khơi gợi mong muốn sở hữu, sử dụng ngôn từ làm nổi bật giá trị độc đáo.",
				"An tâm" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: An tâm. Hãy làm sao để bài thơ này làm rõ sự yên tâm, nhấn mạnh tính an toàn và đảm bảo của sản phẩm/dịch vụ.",
				"Truyền cảm hứng" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Truyền cảm hứng. Hãy làm sao để bài thơ này thể hiện sự lạc quan và động lực để khách hàng cảm thấy được khích lệ.",
				"Tò mò" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Tò mò. Hãy làm sao để bài thơ này khơi dậy sự tò mò, sử dụng ngôn từ làm người đọc muốn tìm hiểu thêm.",
				"Yêu thương" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Yêu thương. Hãy làm sao để bài thơ này thể hiện sự quan tâm, tình cảm dành cho bản thân hoặc gia đình.",
				"Hài hước" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Hài hước. Hãy làm sao để bài thơ này mang phong cách dí dỏm, tạo cảm giác thoải mái và vui vẻ.",
				"Hoài niệm" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Hoài niệm. Hãy làm sao để bài thơ này gợi nhớ những kỷ niệm đẹp và cảm xúc ấm áp trong quá khứ.",
				"Cảm động" => "Hãy viết lại bài thơ này với Cảm xúc bài thơ này là: Cảm động. Hãy làm sao để bài thơ này mang đến cảm giác xúc động, thể hiện tình cảm sâu sắc và chân thành."
			];

			$promptsPhongCach = [
				"Hài hước" => "Hãy viết lại bài thơ này với phong cách bài thơ này là: Hài hước. Hãy sử dụng ngôn từ trong bài thơ mang phong cách hài hước, sử dụng ngôn ngữ dí dỏm và vui nhộn để tạo cảm giác thoải mái và giải trí cho người đọc.",
				"Lãng mạn" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Lãng mạn. Hãy sử dụng ngôn ngữ trong bài thơ mang phong cách lãng mạn, sử dụng từ ngữ bay bổng, nhẹ nhàng, tạo cảm giác tình cảm sâu sắc và tinh tế.",
				"Cảm động" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Cảm động. Hãy sử dụng ngôn ngữ trong bài thơ mang phong cách cảm động, sử dụng ngôn từ xúc động, sâu sắc để chạm đến trái tim người đọc.",
				"Sôi động" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Sôi động. Hãy sử dụng ngôn từ trong bài thơ mang phong cách sôi động, sử dụng ngôn ngữ mạnh mẽ, đầy năng lượng để truyền cảm hứng và động lực.",
				"Cổ điển" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Cổ điển. Hãy sử dụng ngôn từ trong bài thơ mang phong cách cổ điển, sử dụng ngôn từ truyền thống, mượt mà và gợi nhớ phong vị văn hóa xưa.",
				"Hiện đại" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Hiện đại. Hãy sử dụng ngôn từ trong bài thơ mang phong cách hiện đại, sử dụng ngôn ngữ trẻ trung, phá cách và gần gũi với xu hướng ngày nay.",
				"Trang trọng" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Trang trọng. Hãy sử dụng ngôn từ trong bài thơ mang phong cách trang trọng, sử dụng ngôn từ lịch sự, nghiêm túc, phù hợp với bối cảnh chuyên nghiệp.",
				"Thân thiện" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Thân thiện. Hãy sử dụng ngôn từ trong bài thơ mang phong cách thân thiện, sử dụng ngôn ngữ gần gũi, dễ hiểu và tạo cảm giác gần gũi với người đọc.",
				"Tinh nghịch" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Tinh nghịch. Hãy sử dụng ngôn từ trong bài thơ mang phong cách tinh nghịch, sử dụng ngôn ngữ trẻ trung, hài hước và phù hợp với những tình huống vui nhộn.",
				"Giản dị" => "Hãy viết lại bài thơ này với Phong cách bài thơ này là: Giản dị. Hãy sử dụng ngôn từ trong bài thơ mang phong cách giản dị, sử dụng ngôn ngữ đơn giản, chân thực, phù hợp với đời sống thường ngày."
			];

			$promptsDinhDang = [
				"Làm lại 1 bài khác có vần hơn!" => "Làm lại bài thơ này, giữ nguyên nội dung và cách làm, nhưng làm lại với lời lẽ vần hơn theo đúng thể thơ Lục Bát. Tuân thủ nguyên tắc gieo vần của thể thơ Lục Bát  truyền thống của Việt Nam với nhịp nhàng 6 chữ - 8 chữ. Ví dụ:\"Xuân về rực rỡ cỏ hoa,Chúc ông sức khỏe, chan hòa niềm vui.\"",
				"Làm lại 1 bài ngắn hơn" => "Làm lại bài thơ này, giữ nguyên nội dung và cách làm, nhưng làm ngắn gọn hơn.",
				"Làm lại 1 bài dài hơn!" => "Làm lại bài thơ này, giữ nguyên nội dung và cách làm, nhưng làm dài và chi tiết hơn."
			];

			$prompt = "";
			if (!empty($mucTieu)) {
				$prompt = $promptsMucTieu[$mucTieu] ?? '';
			} elseif (!empty($camXuc)) {
				$prompt = $promptsCamXuc[$camXuc] ?? '';
			} elseif (!empty($phongCach)) {
				$prompt = $promptsPhongCach[$phongCach] ?? '';
			} elseif (!empty($dinhDang)) {
				$prompt = $promptsDinhDang[$dinhDang] ?? '';
			}

			if (!empty($tuKhoa)) {
				$prompt = !empty($prompt) ?
					"$prompt \nLàm lại bài thơ này, giữ nguyên nội dung, bổ sung thêm từ khóa là: $tuKhoa" :
					"Làm lại bài thơ này, giữ nguyên nội dung, bổ sung thêm từ khóa là: $tuKhoa";
			}

			if (empty($prompt)) {
				$prompt = "Không tìm thấy tiêu chí phù hợp";
			}

			return [
				"prompt" => $prompt,
				"text" => $text
			];
		} catch (\Exception $e) {
			return [
				"prompt" => "Error: Invalid input data",
				"text" => ""
			];
		}
	}

	private function buoc5GenerateNhacQuangCaoPrompt(): array
	{
		try {
			$promptsMucTieu = [
				"Bán hàng" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Bán hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh lợi ích và giá trị của sản phẩm/dịch vụ, tạo động lực để khách hàng quyết định mua ngay.",
				"Giới thiệu thương hiệu" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Giới thiệu thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật giá trị cốt lõi và hình ảnh thương hiệu, giúp khách hàng ghi nhớ.",
				"Tạo tương tác" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Tạo tương tác. Hãy tạo ra 1 bài hát để có được mục đích cuối là khuyến khích khách hàng tham gia bình luận, chia sẻ hoặc thảo luận về sản phẩm/dịch vụ.",
				"Tăng nhận diện thương hiệu" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Tăng nhận diện thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật thương hiệu với phong cách độc đáo, dễ nhớ.",
				"Khuyến khích sử dụng dịch vụ" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Khuyến khích sử dụng dịch vụ. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh sự tiện lợi và lợi ích khi khách hàng chọn dịch vụ này.",
				"Kích thích tò mò" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Kích thích tò mò. Hãy tạo ra 1 bài hát để có được mục đích cuối là khơi dậy sự tò mò của khách hàng, khiến họ muốn tìm hiểu thêm về sản phẩm/dịch vụ.",
				"Chào mừng sự kiện" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Chào mừng sự kiện. Hãy tạo ra 1 bài hát để có được mục đích cuối là để kỷ niệm hoặc quảng bá cho một sự kiện đặc biệt của thương hiệu.",
				"Cảm ơn khách hàng" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Cảm ơn khách hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là thể hiện lòng biết ơn sâu sắc đối với khách hàng đã đồng hành cùng thương hiệu.",
				"Truyền cảm hứng" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Truyền cảm hứng. Hãy tạo ra 1 bài hát để có được mục đích cuối là mang đến động lực và niềm tin tích cực cho khách hàng.",
				"Giải trí" => "Hãy viết lại bài hát này với Mục đích bài hát này là: Giải trí. Hãy tạo ra 1 bài hát để có được mục đích cuối là với nội dung vui nhộn, nhẹ nhàng, tạo cảm giác thoải mái cho người đọc."
			];

			$promptsCamXuc = [
				"Tin tưởng" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tin tưởng. Hãy viết bài quảng cáo tập trung vào việc xây dựng niềm tin, nhấn mạnh chứng nhận, đánh giá từ khách hàng và uy tín của sản phẩm.",
				"Hứng khởi" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hứng khởi. Hãy tạo bài viết làm nổi bật sự mới lạ, độc đáo của sản phẩm, khiến khách hàng cảm thấy hứng khởi và muốn khám phá ngay.",
				"Khẩn cấp" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khẩn cấp. Viết bài kèm thông điệp khẩn cấp, sử dụng ngôn ngữ nhấn mạnh ưu đãi có thời hạn hoặc số lượng giới hạn để thúc đẩy khách hàng hành động.",
				"Hạnh phúc" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là niềm hạnh phúc. Hãy viết bài mô tả cảm giác hạnh phúc và niềm vui mà khách hàng có thể trải nghiệm khi sử dụng sản phẩm/dịch vụ.",
				"Tự hào" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tự hào. Tạo bài viết nhấn mạnh giá trị đẳng cấp và lý do khách hàng sẽ cảm thấy tự hào khi sử dụng sản phẩm.",
				"Thấu hiểu" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự thấu hiểu. Hãy tạo bài viết thể hiện sự đồng cảm với khó khăn của khách hàng và cách sản phẩm/dịch vụ giúp họ giải quyết vấn đề.",
				"Khao khát" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khao khát. Viết bài tập trung vào việc khơi gợi khao khát của khách hàng bằng cách nhấn mạnh giá trị và lợi ích độc đáo của sản phẩm.",
				"An tâm" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự an tâm. Tạo nội dung làm rõ các cam kết, bảo hành, và chứng minh tính an toàn, hiệu quả của sản phẩm.",
				"Truyền cảm hứng" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự cảm hứng. Hãy viết bài mang tính truyền cảm hứng, nhấn mạnh những thay đổi tích cực mà sản phẩm/dịch vụ có thể mang lại.",
				"Tò mò" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tò mò. Viết bài mở đầu với một câu chuyện hoặc dữ liệu thú vị, làm khách hàng tò mò và muốn tìm hiểu chi tiết hơn.",
				"Yêu thương" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự yêu thương. Hãy tạo bài viết làm nổi bật cách sản phẩm/dịch vụ mang lại sự yêu thương, chăm sóc cho bản thân hoặc gia đình.",
				"Vui vẻ Hài hước" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hài hước. Hãy viết bài quảng cáo hài hước và dí dỏm để tạo sự thích thú và dễ dàng chia sẻ trên mạng xã hội.",
				"Động lực" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự động lực. Hãy viết bài quảng cáo mang tính khích lệ, tập trung vào việc thúc đẩy khách hàng thực hiện hành động cụ thể ngay lập tức.",
				"Hoài niệm" => "Hãy viết lại bài hát này với Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hoài niệm. Viết bài quảng cáo gợi nhớ những kỷ niệm đẹp trong quá khứ và kết nối cảm xúc với sản phẩm/dịch vụ."
			];

			$promptsPhongCach = [
				"Trang Trọng" => "Hãy viết lại bài hát này với Phong cách viết: Tạo ra một phong cách trang trọng cho nội dung, phù hợp với môi trường kinh doanh chính thức hoặc các sự kiện quan trọng.",
				"Hấp Dẫn và Sáng Tạo" => "Hãy viết lại bài hát này với Phong cách viết: Làm cho nội dung trở nên sáng tạo và độc đáo, thu hút sự chú ý bằng cách thể hiện một cách tiếp cận mới lạ.",
				"Thư Giãn và Thân Thiện" => "Hãy viết lại bài hát này với Phong cách viết: Tạo nội dung để nó trở nên thân thiện và dễ chịu, tạo môi trường thư giãn cho người đọc.",
				"Chuyên Nghiệp" => "Hãy viết lại bài hát này với Phong cách viết: Tăng cường tính chuyên nghiệp và uy tín của nội dung, sử dụng ngôn ngữ chính xác và thông tin đáng tin cậy.",
				"Hài hước và vui vẻ" => "Hãy viết lại bài hát này với Phong cách viết: Thêm yếu tố hài hước và niềm vui vào nội dung, làm cho nó trở nên sinh động và giải trí.",
				"Thể Thao và Năng Động" => "Hãy viết lại bài hát này với Phong cách viết: Phản ánh tính năng động và sôi động, thích hợp với những sản phẩm liên quan đến thể thao hoặc hoạt động ngoài trời.",
				"Hướng Dẫn và Giảng Dạy" => "Hãy viết lại bài hát này với Phong cách viết: Đưa nội dung thành một hướng dẫn hoặc bài giảng, cung cấp thông tin hữu ích và kiến thức cho người đọc.",
				"Chất Lượng và Tinh Tế" => "Hãy viết lại bài hát này với Phong cách viết: Tăng cường chất lượng và tinh tế trong nội dung, nhấn mạnh sự chăm chút và độ chính xác của thông tin.",
				"Ngắn Gọn và Súc Tích" => "Hãy viết lại bài hát này với Phong cách viết: Làm cho nội dung trở nên ngắn gọn và đến trực tiếp điểm, loại bỏ mọi thông tin thừa.",
				"Chia Sẻ Kinh Nghiệm Cá Nhân" => "Hãy viết lại bài hát này với Phong cách viết: Thêm vào nội dung những trải nghiệm cá nhân hoặc câu chuyện từ người viết, làm cho nó trở nên thực tế và có liên quan hơn.",
				"Truyện Cười và Giải Trí" => "Hãy viết lại bài hát này với Phong cách viết: Biến nội dung thành một phần giải trí, thêm truyện cười hoặc yếu tố giải trí để thu hút người đọc.",
				"Đánh Giá và So Sánh" => "Hãy viết lại bài hát này với Phong cách viết: Thêm phần đánh giá hoặc so sánh sản phẩm/dịch vụ với các lựa chọn khác, cung cấp thông tin chi tiết và khách quan.",
				"Cảm Xúc và Sâu Sắc" => "Hãy viết lại bài hát này với Phong cách viết: Tạo nội dung với cảm xúc sâu sắc, sử dụng ngôn từ mô tả cảm xúc và trải nghiệm đa chiều.",
				"Tone Nghiêm túc" => "Hãy viết lại bài hát này với Phong cách viết: Nội dung nghiêm túc và uy tín, thích hợp cho các thông điệp quan trọng.",
				"Tone Lạc quan" => "Hãy viết lại bài hát này với Phong cách viết: Làm cho nội dung trở nên lạc quan và tích cực hơn, truyền đạt sự tự tin và khả năng của sản phẩm.",
				"Tone Hấp Dẫn và Thú vị" => "Hãy viết lại bài hát này với Phong cách viết: Nội dung hấp dẫn và thú vị, thu hút sự chú ý của người đọc.",
				"Tone Thư Thái, nhẹ nhàng" => "Hãy viết lại bài hát này với Phong cách viết: Nội dung tạo cảm giác thư thái và nhẹ nhàng, giúp người đọc cảm thấy thoải mái khi tiếp nhận thông tin."
			];

			$promptsDinhDang = [
				"Tiêu đề hấp dẫn hơn" => "Làm lại bài hát này, giữ nguyên nội dung và cách làm, nhưng thay đổi cho tiêu đề hấp dẫn hơn, tạo sự tò mò hơn.",
				"Làm lại 1 bài ngắn hơn!" => "Làm lại bài hát này, giữ nguyên nội dung và cách làm, nhưng làm ngắn gọn hơn.",
				"Làm lại 1 bài dài hơn!" => "Làm lại bài hát này, giữ nguyên nội dung và cách làm, nhưng làm dài và chi tiết hơn."
			];

			$prompt = "";
			if (!empty($mucTieu)) {
				$prompt = $promptsMucTieu[$mucTieu] ?? '';
			} elseif (!empty($camXuc)) {
				$prompt = $promptsCamXuc[$camXuc] ?? '';
			} elseif (!empty($phongCach)) {
				$prompt = $promptsPhongCach[$phongCach] ?? '';
			} elseif (!empty($dinhDang)) {
				$prompt = $promptsDinhDang[$dinhDang] ?? '';
			}

			if (!empty($tuKhoa)) {
				$prompt = !empty($prompt) ?
					"$prompt \nLàm lại bài hát này, giữ nguyên nội dung, bổ sung thêm từ khóa là: $tuKhoa" :
					"Làm lại bài hát này, giữ nguyên nội dung, bổ sung thêm từ khóa là: $tuKhoa";
			}

			if (empty($prompt)) {
				$prompt = "Không tìm thấy tiêu chí phù hợp";
			}

			return [
				"prompt" => $prompt,
				"text" => $text
			];
		} catch (\Exception $e) {
			return [
				"prompt" => "Error: Invalid input data",
				"text" => ""
			];
		}
	}

	private function buoc5GenerateChienDichTruyenThongPrompt(): array
	{
		return [
			"prompt" => "",
			"text" => ""
		];
	}
}
