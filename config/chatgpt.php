<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'transcriptions_audio_endpoint' => env('TRANSCRIPTIONS_AUDIO_ENDPOINT', 'https://api.openai.com/v1/audio/transcriptions'),
    'transcriptions_model' => env('TRANSCRIPTIONS_MODEL', 'whisper-1'),
    'simple-message' => 'Bạn là trợ lý ảo AI, bạn sẽ hỗ trợ giải đáp yêu cầu cho người dùng. Yêu cầu là: ":mesage" hãy giải đáp chi tiết bằng ":lang". Đây là nội dung file mà người dùng cung cấp: ":file_content"',
    'simple-message-text-only' => 'Bạn là trợ lý ảo AI, bạn sẽ hỗ trợ giải đáp yêu cầu cho người dùng. Yêu cầu là: ":mesage" hãy giải đáp chi tiết bằng ":lang".',
    'ai-text' => 'Bạn là trợ lý ảo AI, Bạn sẽ hỗ trợ tạo văn bản cho người dùng trên nền tảng ":platform". yêu cầu là ":mesage", hãy viết với một chất giọng ":voice" cho đối tượng là ":object", giới hạn nội dung ở ":limit" từ và hãy trả lời bằng ":lang"',
    'seo-generate' => 'Bạn là trợ lý ảo AI, bạn sẽ hỗ trợ viết bài SEO đúng chuẩn. Yêu cầu là: ":prompt" hãy viết bằng ":lang". Đây là nội dung file mà người dùng cung cấp: ":file_content". Đây là đoạn văn bản ban đầu của người dùng ":posting". Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu.',
    'ai-text-2' => 'Bạn là chuyên gia trong lĩnh vực ":field". Hãy hỗ trợ tạo văn bản cho người dùng bằng cách trả lời chính xác theo chủ đề và mô tả yêu cầu. Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu. Trình bày dưới dạng liệt kê dễ nhìn, hết câu hãy xuống dòng dùng thẻ <br> dính sát với ký tự sau nó và không được có khoảng cách, tuyệt đối không được dùng ký tự \n. Chủ đề là ":message". Nội dung cần mô tả là ":desciption". Giới hạn nội dung không quá ":limit" từ và hãy trả lời bằng ":lang"',
    'ai-create-topic' => 'Bạn là trợ lý ảo AI. Hãy hỗ trợ gợi ý chủ đề liên quan đến mô tả yêu cầu. Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu. Nội dung cần mô tả là ":description". Số lượng chủ đề cần là :limit. Không được dùng ký tự \n.  Hãy trả lời bằng ":lang".  Hãy trả về một array object JSON (array object) dưới định dạng như sau mà không thêm bất kỳ kí hiệu nào khác (như ```json): [\"<content>\"]. Chỉ cần trả về định dạng như yêu cầu. Không đánh số thứ tự',
    'ai-create-content' => 'Bạn là trợ lý ảo AI. Bạn sẽ hỗ trợ tạo văn bản cho người dùng trên nền tảng ":platform". Viết một bài viết liên quan về mô tả yêu cầu. Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu. Nội dung cần mô tả là ":description". Bài viết dạng AIDAS . Giới hạn số từ bài viết khoảng ":limit" từ và hãy trả lời bằng ":lang".Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu *,, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown. Không được tạo các ký tự đặc biệt bừa bãi  ',
    'languages' => [
        'en' => 'English',
        'vi' => 'Vietnamese',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ko' => 'Korean'
    ],
    'prompt_translate' => 'Translate this sentence into :language: ":sentence" without any extra formatting or escape characters.',
    'grade_prompt' => 'Đây là công thức đánh giá bài viết Công thức đánh giá sức hút bài viết Tổng điểm = A × W1 + B × W2 + C × W3 + D × W4 + E × W5 + F × W6 + G × W7 . Trong đó: * A: Sức hấp dẫn của tiêu đề (10 điểm) * Có từ khóa hấp dẫn, gây tò mò, kêu gọi hành động? * Có sử dụng con số, câu hỏi hoặc từ mạnh không? * B: Độ dài hợp lý (10 điểm) * Bài viết có độ dài phù hợp với thể loại và nền tảng không? * Không quá dài hoặc quá ngắn so với mục tiêu bài viết? * C: Độ rõ ràng & mạch lạc (15 điểm) * Cấu trúc dễ đọc, đoạn văn ngắn, ngôn từ dễ hiểu? * Không có câu quá dài, không lan man, không sai chính tả? * D: Cảm xúc truyền tải (15 điểm) * Bài viết có chạm đến cảm xúc người đọc không? * Có yếu tố hài hước, cảm động, giật gân hay truyền cảm hứng không? * E: Tính độc đáo (20 điểm) * Nội dung mới lạ, góc nhìn riêng, không rập khuôn? * Không bị trùng lặp hoặc sáo rỗng? * F: Giá trị thông tin (20 điểm) * Bài viết có cung cấp thông tin hữu ích, có tính thực tiễn cao? * Có số liệu, ví dụ cụ thể, dẫn chứng đáng tin cậy không? * G: Kêu gọi hành động & tương tác (10 điểm) * Bài viết có khuyến khích người đọc tương tác (bình luận, chia sẻ)? * Có câu hỏi mở hoặc kêu gọi hành động không? Hệ số quan trọng (W1 - W7)Mỗi yếu tố có mức độ quan trọng khác nhau, ví dụ: * W1 = 1.0 (Tiêu đề quan trọng nhưng không quyết định tất cả) * W2 = 0.8 (Độ dài ảnh hưởng nhưng không phải yếu tố chính) * W3 = 1.2 (Độ rõ ràng quan trọng) * W4 = 1.3 (Cảm xúc mạnh giúp bài viral hơn) * W5 = 1.5 (Tính độc đáo là yếu tố quan trọng nhất) * W6 = 1.5 (Giá trị thông tin ảnh hưởng đến người đọc lâu dài) * W7 = 1.0 (Tương tác giúp bài lan truyền tốt hơn). Hãy dựa vào công thức được cung cấp để chấm điểm và đưa ra đánh giá cho nội dung sau đây: ":content". Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu.',
    'improve_prompt' => 'Bạn là chuyên gia tạo content, tôi sẽ cung cấp công thức đánh giá nội dung. Hãy dựa vào đó để cải thiện nội dung tôi cung cấp tăng điểm đánh giá và chất lượng bài viết của tôi. Đây là công thức đánh giá bài viết Công thức đánh giá sức hút bài viết Tổng điểm = A × W1 + B × W2 + C × W3 + D × W4 + E × W5 + F × W6 + G × W7 . Trong đó: * A: Sức hấp dẫn của tiêu đề (10 điểm) * Có từ khóa hấp dẫn, gây tò mò, kêu gọi hành động? * Có sử dụng con số, câu hỏi hoặc từ mạnh không? * B: Độ dài hợp lý (10 điểm) * Bài viết có độ dài phù hợp với thể loại và nền tảng không? * Không quá dài hoặc quá ngắn so với mục tiêu bài viết? * C: Độ rõ ràng & mạch lạc (15 điểm) * Cấu trúc dễ đọc, đoạn văn ngắn, ngôn từ dễ hiểu? * Không có câu quá dài, không lan man, không sai chính tả? * D: Cảm xúc truyền tải (15 điểm) * Bài viết có chạm đến cảm xúc người đọc không? * Có yếu tố hài hước, cảm động, giật gân hay truyền cảm hứng không? * E: Tính độc đáo (20 điểm) * Nội dung mới lạ, góc nhìn riêng, không rập khuôn? * Không bị trùng lặp hoặc sáo rỗng? * F: Giá trị thông tin (20 điểm) * Bài viết có cung cấp thông tin hữu ích, có tính thực tiễn cao? * Có số liệu, ví dụ cụ thể, dẫn chứng đáng tin cậy không? * G: Kêu gọi hành động & tương tác (10 điểm) * Bài viết có khuyến khích người đọc tương tác (bình luận, chia sẻ)? * Có câu hỏi mở hoặc kêu gọi hành động không? Hệ số quan trọng (W1 - W7)Mỗi yếu tố có mức độ quan trọng khác nhau, ví dụ: * W1 = 1.0 (Tiêu đề quan trọng nhưng không quyết định tất cả) * W2 = 0.8 (Độ dài ảnh hưởng nhưng không phải yếu tố chính) * W3 = 1.2 (Độ rõ ràng quan trọng) * W4 = 1.3 (Cảm xúc mạnh giúp bài viral hơn) * W5 = 1.5 (Tính độc đáo là yếu tố quan trọng nhất) * W6 = 1.5 (Giá trị thông tin ảnh hưởng đến người đọc lâu dài) * W7 = 1.0 (Tương tác giúp bài lan truyền tốt hơn). Hãy dựa vào công thức được cung cấp để cải thiện nội dung sau đây: ":content". Chỉ cần trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu.',
    'postFb' => [
        'category' => [
            'Bài viết Quảng cáo sản phẩm' => [
                'code_5' => 'Hãy tạo ra 1 status để quảng cáo cho Sản phẩm trên của tôi, Status này phải có câu mở đầu hấp dẫn gây shock và tạo sự tò mò. Câu mở đầu nên lấy nội dung từ phần Trending để chạm vào tâm trí đám đông trước khi viết nội dung quảng cáo.
                Mục tiêu: {muc_tieu}.
                Cảm xúc mong muốn: {cam_xuc}.
                Phong cách thể hiện: {phong_cach}.
                Cùng với các đề bài cho Status này như sau: ',
                'system_prompt' => '1. Bối cảnh (Context):
                                 Tôi cần tối ưu một prompt để đảm bảo khi sử dụng, nó giữ được đầy đủ thông tin gốc, không bị lược bỏ hoặc thay đổi nội dung quan trọng.
                                 2. Yêu cầu chính (Main Request):
                                 Hãy viết lại prompt bên dưới theo cấu trúc rõ ràng, giúp hệ thống hiểu đúng yêu cầu mà không làm mất bất kỳ dữ liệu nào từ nội dung gốc.
                                 3. Chi tiết cụ thể (Specific Details):
                                 Giữ nguyên mọi thông tin có trong prompt gốc, không được bỏ sót hoặc thay thế nội dung bằng cụm từ chung chung.
                                 Sắp xếp lại thông tin theo cấu trúc hợp lý: Bối cảnh, Yêu cầu chính, Chi tiết cụ thể, Kỳ vọng đầu ra, Yêu cầu bổ sung.
                                 Nếu cần, có thể cải thiện câu từ để tăng tính rõ ràng, nhưng không làm thay đổi ý nghĩa.
                                 4. Kỳ vọng đầu ra (Expected Output):
                                 Một prompt hoàn chỉnh, có đầy đủ thông tin như nội dung gốc.
                                 Cấu trúc mạch lạc, giúp hệ thống hiểu và xử lý chính xác yêu cầu.
                                 Không được làm mất hoặc đơn giản hóa thông tin quan trọng.
                                 5. Yêu cầu bổ sung (Optional Constraints):
                                 Không thay thế nội dung gốc bằng các cụm từ như "với toàn bộ nội dung chi tiết như được cung cấp".
                                 Không thêm thông tin không có trong prompt gốc.
                                 Không xác nhận yêu cầu hoặc giải thích lại, chỉ trả về nội dung đã tối ưu.',
                'user_prompt' => '{code_5_result}
                                       {bai_viet_qc_result}.
                                       Thông tin cho sản phẩm của tôi là : {thong_tin_dau_vao} và {ten_du_an}.

                                       Chú ý:
                                       - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                       - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                       - Độ dài status: {min} - {max} từ
                                       - Trả lời bằng Tiếng Việt.',
                'recheck_prompt' => '{result}
Chú ý:
- Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
- Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
- Độ dài status: {min} - {max} từ
- Trả lời bằng Tiếng Việt.'
            ],
            'Thơ Quảng cáo sản phẩm' => [
                'code_5' => 'Hãy làm 1 bài thơ theo thể thơ Lục Bát. Độ dài 3 khổ - tương ứng với 12 dòng. Tuân thủ nguyên tắc gieo vần của thể thơ Lục Bát truyền thống của Việt Nam với nhịp nhàng 6 chữ - 8 chữ. Ví dụ: Xuân về rực rỡ cỏ hoa,Chúc ông sức khỏe, chan hòa niềm vui.Với các thông tin đề bài như sau: ',
                'system_prompt' => 'Bạn là 1 chuyên gia viết prompt, hãy viết lại prompt bên dưới giúp tôi theo cấu trúc sau:
                                    1. Bối cảnh (Context)
                                    Giúp hệ thống hiểu rõ tình huống, mục đích hoặc lý do bạn cần kết quả này.
                                    Ví dụ: "Tôi đang xây dựng kênh YouTube về lòng biết ơn và việc tử tế, nhằm truyền cảm hứng tích cực đến người xem."
                                    2. Yêu cầu chính (Main Request)
                                    Nêu rõ bạn muốn hệ thống làm gì, tránh mơ hồ.
                                    Ví dụ: "Hãy viết cho tôi một kịch bản video dài 3 phút về sức mạnh của lòng biết ơn."
                                    3. Chi tiết cụ thể (Specific Details)
                                    Thêm các thông tin chi tiết để định hướng kết quả như: độ dài, phong cách, tông giọng, cấu trúc…
                                    Ví dụ: "Tông giọng nhẹ nhàng, truyền cảm. Nội dung có câu chuyện dẫn dắt và kết thúc bằng một thông điệp tích cực."
                                    4. Kỳ vọng đầu ra (Expected Output)
                                    Mô tả kết quả bạn muốn nhận được: bài viết, danh sách ý tưởng, kịch bản chi tiết, v.v.
                                    Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                    Ví dụ: "Kết quả là một đoạn kịch bản đầy đủ, có lời dẫn và gợi ý hình ảnh minh hoạ."
                                    5. Yêu cầu bổ sung (Optional Constraints)
                                    Những điều nên tránh hoặc giới hạn cụ thể.
                                    Ví dụ: "Không sử dụng các câu từ mang tính tiêu cực hoặc gây tranh cãi."
                                    Chú ý:
                                    - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                    - Không được thay thế nội dung gốc bằng các đoạn như \'(với toàn bộ nội dung chi tiết như được cung cấp)\'.
                                    - Hãy tối ưu prompt mà không làm mất dữ liệu gốc.',
                'user_prompt' => 'Đây là prompt cần viết lại:
                                              "Hãy sáng tác một bài thơ Lục Bát gồm 3 khổ (12 câu), tuân thủ các quy tắc sau:
                                              Mỗi khổ gồm 4 câu: câu 6 chữ xen kẽ với câu 8 chữ
                                              Câu 6 chữ (lục) có vần cuối bằng
                                              Câu 8 chữ (bát) có vần cuối trắc
                                              Từ cuối câu lục phải vần với từ thứ 6 của câu bát kế tiếp
                                              Từ cuối câu bát phải vần với từ cuối câu lục tiếp theo
                                              Giọng điệu thơ phải phù hợp với chủ đề và mang tính nhạc điệu
                                              {code_5_result}
                                              Bài thơ viết cho sản phẩm này của tôi, đây là các thông tin sản phẩm của tôi : {thong_tin_dau_vao} và {ten_du_an} .
                                              Mục tiêu: {muc_tieu}.
                                              Cảm xúc mong muốn: {cam_xuc}.
                                              Phong cách thể hiện: {phong_cach}.
                                              Chú ý:
                                              - Dùng từ thuần Việt, hạn chế từ Hán-Việt hoặc tiếng Anh
                                              - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                              - Trả lời bằng Tiếng Việt."'
            ],
            'Nhạc Doanh nghiệp' => [
                'code_5' => "Tôi lựa chọn ý tưởng bên trên.  Xin hãy tạo một lời bài hát sử dụng ý tưởng này để tạo ra 1 lyric. Lyric sẽ có cấu trúc Verse - Chorus - Verse, và tôi muốn lyric có cấu trúc vần AABB và các dòng có vần cuối giống nhau, tạo ra một cấu trúc vần đặc biệt và hài hòa. Chú ý sử dụng ngôn từ đơn giản mộc mạc, dể hiểu, gần gũi, để dễ dàng phối nhạc. Trong lyric hãy cho tôi tên thương hiệu dựa trên thông tin sản phẩm bên trên",
                'system_prompt' => 'Bạn là 1 chuyên gia viết prompt, hãy viết lại prompt bên dưới giúp tôi theo cấu trúc sau:
                                    1. Bối cảnh (Context)
                                    Giúp hệ thống hiểu rõ tình huống, mục đích hoặc lý do bạn cần kết quả này.
                                    Ví dụ: "Tôi đang xây dựng kênh YouTube về lòng biết ơn và việc tử tế, nhằm truyền cảm hứng tích cực đến người xem."
                                    2. Yêu cầu chính (Main Request)
                                    Nêu rõ bạn muốn hệ thống làm gì, tránh mơ hồ.
                                    Ví dụ: "Hãy viết cho tôi một kịch bản video dài 3 phút về sức mạnh của lòng biết ơn."
                                    3. Chi tiết cụ thể (Specific Details)
                                    Thêm các thông tin chi tiết để định hướng kết quả như: độ dài, phong cách, tông giọng, cấu trúc…
                                    Ví dụ: "Tông giọng nhẹ nhàng, truyền cảm. Nội dung có câu chuyện dẫn dắt và kết thúc bằng một thông điệp tích cực."
                                    4. Kỳ vọng đầu ra (Expected Output)
                                    Mô tả kết quả bạn muốn nhận được: bài viết, danh sách ý tưởng, kịch bản chi tiết, v.v.
                                    Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                    Ví dụ: "Kết quả là một đoạn kịch bản đầy đủ, có lời dẫn và gợi ý hình ảnh minh hoạ."
                                    5. Yêu cầu bổ sung (Optional Constraints)
                                    Những điều nên tránh hoặc giới hạn cụ thể.
                                    Ví dụ: "Không sử dụng các câu từ mang tính tiêu cực hoặc gây tranh cãi."
                                    Chú ý:
                                    - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                    - Không được thay thế nội dung gốc bằng các đoạn như \'(với toàn bộ nội dung chi tiết như được cung cấp)\'.
                                    - Hãy tối ưu prompt mà không làm mất dữ liệu gốc.',
                'user_prompt' => 'Đây là prompt cần viết lại:
                                       Thông tin cho sản phẩm của tôi là : {thong_tin_dau_vao} và {ten_du_an}.
                                       Mục tiêu: {muc_tieu}
                                       Cảm xúc mong muốn: {cam_xuc}.
                                       Phong cách thể hiện: {phong_cach}.
                                       Ý tưởng là : {y_tuong}
                                       {code_5_result}
                                       Chú ý:
                                       - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                       - Trả lời bằng Tiếng Việt."'

            ],
            'Chiến dịch Truyền thông' => [
                'code_5' => '',
                'system_prompt' => 'Bạn là 1 chuyên gia viết prompt, hãy viết lại prompt bên dưới giúp tôi theo cấu trúc sau:
                                    1. Bối cảnh (Context)
                                    Giúp hệ thống hiểu rõ tình huống, mục đích hoặc lý do bạn cần kết quả này.
                                    Ví dụ: "Tôi đang xây dựng kênh YouTube về lòng biết ơn và việc tử tế, nhằm truyền cảm hứng tích cực đến người xem."
                                    2. Yêu cầu chính (Main Request)
                                    Nêu rõ bạn muốn hệ thống làm gì, tránh mơ hồ.
                                    Ví dụ: "Hãy viết cho tôi một kịch bản video dài 3 phút về sức mạnh của lòng biết ơn."
                                    3. Chi tiết cụ thể (Specific Details)
                                    Thêm các thông tin chi tiết để định hướng kết quả như: độ dài, phong cách, tông giọng, cấu trúc…
                                    Ví dụ: "Tông giọng nhẹ nhàng, truyền cảm. Nội dung có câu chuyện dẫn dắt và kết thúc bằng một thông điệp tích cực."
                                    4. Kỳ vọng đầu ra (Expected Output)
                                    Mô tả kết quả bạn muốn nhận được: bài viết, danh sách ý tưởng, kịch bản chi tiết, v.v.
                                    Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                    Ví dụ: "Kết quả là một đoạn kịch bản đầy đủ, có lời dẫn và gợi ý hình ảnh minh hoạ."
                                    5. Yêu cầu bổ sung (Optional Constraints)
                                    Những điều nên tránh hoặc giới hạn cụ thể.
                                    Ví dụ: "Không sử dụng các câu từ mang tính tiêu cực hoặc gây tranh cãi."
                                    Chú ý:
                                    - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                    - Không được thay thế nội dung gốc bằng các đoạn như \'(với toàn bộ nội dung chi tiết như được cung cấp)\'.
                                    - Hãy tối ưu prompt mà không làm mất dữ liệu gốc.',
                'user_prompt' => 'Đây là prompt cần viết lại:
                              Thông tin cho sản phẩm của tôi là : {thong_tin_dau_vao} và {ten_du_an}.
                              Mục tiêu: {muc_tieu}
                              Cảm xúc mong muốn: {cam_xuc}.
                              Phong cách thể hiện: {phong_cach}.
                              Chú ý:
                              - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                              - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                              - Trả lời bằng Tiếng Việt."'
            ],
        ]
    ],
    'difyai' => [
            'category' => [
                'Bài viết Quảng cáo sản phẩm' => [
                        'code_5' => 'Hãy tạo ra 1 status để quảng cáo cho Sản phẩm trên của tôi, Status này phải có câu mở đầu hấp dẫn gây shock và tạo sự tò mò. Câu mở đầu nên lấy nội dung từ phần Trending để chạm vào tâm trí đám đông trước khi viết nội dung quảng cáo. Cùng với các đề bài cho Status này như sau: ',
                        'system_prompt' => '1. Bối cảnh (Context):
                                     Tôi cần tối ưu một prompt để đảm bảo khi sử dụng, nó giữ được đầy đủ thông tin gốc, không bị lược bỏ hoặc thay đổi nội dung quan trọng.
                                     2. Yêu cầu chính (Main Request):
                                     Hãy viết lại prompt bên dưới theo cấu trúc rõ ràng, giúp hệ thống hiểu đúng yêu cầu mà không làm mất bất kỳ dữ liệu nào từ nội dung gốc.
                                     3. Chi tiết cụ thể (Specific Details):
                                     Giữ nguyên mọi thông tin có trong prompt gốc, không được bỏ sót hoặc thay thế nội dung bằng cụm từ chung chung.
                                     Sắp xếp lại thông tin theo cấu trúc hợp lý: Bối cảnh, Yêu cầu chính, Chi tiết cụ thể, Kỳ vọng đầu ra, Yêu cầu bổ sung.
                                     Nếu cần, có thể cải thiện câu từ để tăng tính rõ ràng, nhưng không làm thay đổi ý nghĩa.
                                     4. Kỳ vọng đầu ra (Expected Output):
                                     Một prompt hoàn chỉnh, có đầy đủ thông tin như nội dung gốc.
                                     Cấu trúc mạch lạc, giúp hệ thống hiểu và xử lý chính xác yêu cầu.
                                     Không được làm mất hoặc đơn giản hóa thông tin quan trọng.
                                     5. Yêu cầu bổ sung (Optional Constraints):
                                     Không thay thế nội dung gốc bằng các cụm từ như "với toàn bộ nội dung chi tiết như được cung cấp".
                                     Không thêm thông tin không có trong prompt gốc.
                                     Không xác nhận yêu cầu hoặc giải thích lại, chỉ trả về nội dung đã tối ưu.',
                         'user_prompt' => '{code_5_result}
                                           {bai_viet_qc_result}.
                                           Thông tin cho sản phẩm của tôi là : {thong_tin_dau_vao} và {ten_du_an}.

                                           Chú ý:
                                           - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                           - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                           - Độ dài status: 700 -1200 từ
                                           - Trả lời bằng Tiếng Việt.',
                          'recheck_prompt' => '{result}
    Chú ý:
    - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
    - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
    - Độ dài status: 700 -1200 từ
    - Trả lời bằng Tiếng Việt.'
                    ],
                'Thơ Quảng cáo sản phẩm' => [
                    'code_5' => 'Hãy làm 1 bài thơ theo thể thơ Lục Bát. Độ dài 3 khổ - tương ứng với 12 dòng. Tuân thủ nguyên tắc gieo vần của thể thơ Lục Bát truyền thống của Việt Nam với nhịp nhàng 6 chữ - 8 chữ. Ví dụ: Xuân về rực rỡ cỏ hoa,Chúc ông sức khỏe, chan hòa niềm vui.Với các thông tin đề bài như sau: ',
                    'system_prompt' => 'Bạn là 1 chuyên gia viết prompt, hãy viết lại prompt bên dưới giúp tôi theo cấu trúc sau:
                                        1. Bối cảnh (Context)
                                        Giúp hệ thống hiểu rõ tình huống, mục đích hoặc lý do bạn cần kết quả này.
                                        Ví dụ: "Tôi đang xây dựng kênh YouTube về lòng biết ơn và việc tử tế, nhằm truyền cảm hứng tích cực đến người xem."
                                        2. Yêu cầu chính (Main Request)
                                        Nêu rõ bạn muốn hệ thống làm gì, tránh mơ hồ.
                                        Ví dụ: "Hãy viết cho tôi một kịch bản video dài 3 phút về sức mạnh của lòng biết ơn."
                                        3. Chi tiết cụ thể (Specific Details)
                                        Thêm các thông tin chi tiết để định hướng kết quả như: độ dài, phong cách, tông giọng, cấu trúc…
                                        Ví dụ: "Tông giọng nhẹ nhàng, truyền cảm. Nội dung có câu chuyện dẫn dắt và kết thúc bằng một thông điệp tích cực."
                                        4. Kỳ vọng đầu ra (Expected Output)
                                        Mô tả kết quả bạn muốn nhận được: bài viết, danh sách ý tưởng, kịch bản chi tiết, v.v.
                                        Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                        Ví dụ: "Kết quả là một đoạn kịch bản đầy đủ, có lời dẫn và gợi ý hình ảnh minh hoạ."
                                        5. Yêu cầu bổ sung (Optional Constraints)
                                        Những điều nên tránh hoặc giới hạn cụ thể.
                                        Ví dụ: "Không sử dụng các câu từ mang tính tiêu cực hoặc gây tranh cãi."

                                        Chú ý:
                                        - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                        - Không được thay thế nội dung gốc bằng các đoạn như \'(với toàn bộ nội dung chi tiết như được cung cấp)\'.
                                        - Hãy tối ưu prompt mà không làm mất dữ liệu gốc.',
                                'user_prompt' => 'Đây là prompt cần viết lại:
                                                  "Hãy sáng tác một bài thơ Lục Bát gồm 3 khổ (12 câu), tuân thủ các quy tắc sau:
                                                  Mỗi khổ gồm 4 câu: câu 6 chữ xen kẽ với câu 8 chữ
                                                  Câu 6 chữ (lục) có vần cuối bằng
                                                  Câu 8 chữ (bát) có vần cuối trắc
                                                  Từ cuối câu lục phải vần với từ thứ 6 của câu bát kế tiếp
                                                  Từ cuối câu bát phải vần với từ cuối câu lục tiếp theo
                                                  Giọng điệu thơ phải phù hợp với chủ đề và mang tính nhạc điệu

                                                  {code_5_result}
                                                  Bài thơ viết cho sản phẩm này của tôi, đây là các thông tin sản phẩm của tôi : {thong_tin_dau_vao} và {ten_du_an}

                                                  Chú ý:
                                                  - Dùng từ thuần Việt, hạn chế từ Hán-Việt hoặc tiếng Anh
                                                  - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                                  - Trả lời bằng Tiếng Việt."'
                    ],
                'Nhạc Doanh nghiệp' => [
                    'code_5' => "Tôi lựa chọn ý tưởng bên trên.  Xin hãy tạo một lời bài hát sử dụng ý tưởng này để tạo ra 1 lyric. Lyric sẽ có cấu trúc Verse - Chorus - Verse, và tôi muốn lyric có cấu trúc vần AABB và các dòng có vần cuối giống nhau, tạo ra một cấu trúc vần đặc biệt và hài hòa. Chú ý sử dụng ngôn từ đơn giản mộc mạc, dể hiểu, gần gũi, để dễ dàng phối nhạc. Trong lyric hãy cho tôi tên thương hiệu dựa trên thông tin sản phẩm bên trên",
                    'system_prompt' => 'Bạn là 1 chuyên gia viết prompt, hãy viết lại prompt bên dưới giúp tôi theo cấu trúc sau:
                                        1. Bối cảnh (Context)
                                        Giúp hệ thống hiểu rõ tình huống, mục đích hoặc lý do bạn cần kết quả này.
                                        Ví dụ: "Tôi đang xây dựng kênh YouTube về lòng biết ơn và việc tử tế, nhằm truyền cảm hứng tích cực đến người xem."
                                        2. Yêu cầu chính (Main Request)
                                        Nêu rõ bạn muốn hệ thống làm gì, tránh mơ hồ.
                                        Ví dụ: "Hãy viết cho tôi một kịch bản video dài 3 phút về sức mạnh của lòng biết ơn."
                                        3. Chi tiết cụ thể (Specific Details)
                                        Thêm các thông tin chi tiết để định hướng kết quả như: độ dài, phong cách, tông giọng, cấu trúc…
                                        Ví dụ: "Tông giọng nhẹ nhàng, truyền cảm. Nội dung có câu chuyện dẫn dắt và kết thúc bằng một thông điệp tích cực."
                                        4. Kỳ vọng đầu ra (Expected Output)
                                        Mô tả kết quả bạn muốn nhận được: bài viết, danh sách ý tưởng, kịch bản chi tiết, v.v.
                                        Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                        Ví dụ: "Kết quả là một đoạn kịch bản đầy đủ, có lời dẫn và gợi ý hình ảnh minh hoạ."
                                        5. Yêu cầu bổ sung (Optional Constraints)
                                        Những điều nên tránh hoặc giới hạn cụ thể.
                                        Ví dụ: "Không sử dụng các câu từ mang tính tiêu cực hoặc gây tranh cãi."

                                        Chú ý:
                                        - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                        - Không được thay thế nội dung gốc bằng các đoạn như \'(với toàn bộ nội dung chi tiết như được cung cấp)\'.
                                        - Hãy tối ưu prompt mà không làm mất dữ liệu gốc.',
                         'user_prompt' => 'Đây là prompt cần viết lại:
                                           "Thông tin cho sản phẩm của tôi là : {thong_tin_dau_vao} và {ten_du_an}.
                                           Ý tưởng là : {y_tuong}
                                           {code_5_result}

                                           Chú ý:
                                           - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                           - Trả lời bằng Tiếng Việt."'

                    ],
                'Chiến dịch Truyền thông' => [
                    'code_5' => '',
                    'system_prompt' => 'Bạn là 1 chuyên gia viết prompt, hãy viết lại prompt bên dưới giúp tôi theo cấu trúc sau:
                                        1. Bối cảnh (Context)
                                        Giúp hệ thống hiểu rõ tình huống, mục đích hoặc lý do bạn cần kết quả này.
                                        Ví dụ: "Tôi đang xây dựng kênh YouTube về lòng biết ơn và việc tử tế, nhằm truyền cảm hứng tích cực đến người xem."
                                        2. Yêu cầu chính (Main Request)
                                        Nêu rõ bạn muốn hệ thống làm gì, tránh mơ hồ.
                                        Ví dụ: "Hãy viết cho tôi một kịch bản video dài 3 phút về sức mạnh của lòng biết ơn."
                                        3. Chi tiết cụ thể (Specific Details)
                                        Thêm các thông tin chi tiết để định hướng kết quả như: độ dài, phong cách, tông giọng, cấu trúc…
                                        Ví dụ: "Tông giọng nhẹ nhàng, truyền cảm. Nội dung có câu chuyện dẫn dắt và kết thúc bằng một thông điệp tích cực."
                                        4. Kỳ vọng đầu ra (Expected Output)
                                        Mô tả kết quả bạn muốn nhận được: bài viết, danh sách ý tưởng, kịch bản chi tiết, v.v.
                                        Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                        Ví dụ: "Kết quả là một đoạn kịch bản đầy đủ, có lời dẫn và gợi ý hình ảnh minh hoạ."
                                        5. Yêu cầu bổ sung (Optional Constraints)
                                        Những điều nên tránh hoặc giới hạn cụ thể.
                                        Ví dụ: "Không sử dụng các câu từ mang tính tiêu cực hoặc gây tranh cãi."

                                        Chú ý:
                                        - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                        - Không được thay thế nội dung gốc bằng các đoạn như \'(với toàn bộ nội dung chi tiết như được cung cấp)\'.
                                        - Hãy tối ưu prompt mà không làm mất dữ liệu gốc.',
                'user_prompt' => 'Đây là prompt cần viết lại:
                                  Thông tin cho sản phẩm của tôi là : {thong_tin_dau_vao} và {ten_du_an}. Mục tiêu: {muc_tieu}

                                  Chú ý:
                                  - Chỉ trả về nội dung, không cần xác nhận gì thêm, không nhắc lại yêu cầu, không cần giải thích.
                                  - Kết quả trả về theo dạng văn bản thuần túy, tuyệt đối không sử dụng bất kỳ định dạng Markdown nào, không dùng dấu **,*, _, #, ##, ### hoặc bất kỳ ký hiệu nào thuộc về Markdown.
                                  - Trả lời bằng Tiếng Việt."'
    ],
            ],
            'bai_viet_qc' => [
                'goal_mapping' => [
                "Tăng Nhận Diện Thương Hiệu"=> "Tạo một status nói về phần 'Câu chuyện về Thương hiệu' và nhấn mạnh 'Ưu điểm và giá trị độc đáo (USP)' với những tính năng độc đáo của sản phẩm/dịch vụ, làm nổi bật sự khác biệt và giá trị nổi bật.",
                            "Thông Tin Sản Phẩm/Dịch Vụ"=> "Tạo một status về tính năng của sản phẩm/dịch vụ, nói tới 'Tính năng và giải quyết vấn đề', nhấn mạnh 'Ưu điểm và giá trị độc đáo (USP)', làm nổi bật 'lợi ích cho khách hàng', cách sản phẩm/dịch vụ giải quyết vấn đề cụ thể, tiết kiệm thời gian, tiền bạc, và giải thích nếu không có sản phẩm, khách hàng sẽ gặp những khó khăn gì.",
                            "Tạo trao đổi về Sản Phẩm/Dịch Vụ"=> "Viết một status kêu gọi cộng đồng thảo luận về kinh nghiệm sử dụng sản phẩm/dịch vụ, khuyến khích khách hàng chia sẻ phản hồi và góp ý về sản phẩm/dịch vụ, thể hiện sự lắng nghe và cam kết cải tiến.",
                            "Kích Thích Sự Tương Tác (Engagement)"=> "Tạo một bài viết với mục tiêu thu hút nhiều tương tác, sử dụng chủ đề hấp dẫn và có sức lan tỏa, khuyến khích sự tham gia tự nhiên từ cộng đồng.",
                            "Quảng bá về Tiện ích và Thái độ phục vụ"=> "Tạo bài viết thông báo về 'Dịch vụ hỗ trợ', nhấn mạnh thái độ chăm sóc khách hàng, tiện ích phục vụ, 'Kênh hỗ trợ khách hàng', hệ thống giao hàng và phân phối, thể hiện sự thuận tiện và nhiệt tình.",
                            "Tạo Độ Tin Cậy và Chuyên Môn"=> "Viết một status chia sẻ rõ hơn về 'Chứng nhận và thông tin tin cậy', lợi ích khách hàng từ sản phẩm/dịch vụ, cùng với sự hỗ trợ nhiệt tình từ các 'Kênh hỗ trợ khách hàng'.",
                            "Phá bỏ Rào cản và Tạo động lực mua"=> "Liệt kê 5 rào cản lớn nhất của khách hàng với sản phẩm/dịch vụ, viết một status kể câu chuyện về một trong các rào cản này, giải thích cách sản phẩm giúp vượt qua rào cản, khuyến khích hành động.",
                            "Kích thích hành động mua ngay"=> "Viết một status dựa trên 'Ưu điểm và giá trị độc đáo (USP)', tạo sự hứng thú và kỳ vọng, nhấn mạnh 'Chương trình khuyến mại' và phản hồi tích cực từ khách hàng, kết hợp lời kêu gọi hành động mạnh mẽ như 'Đặt hàng ngay'.",
                            "Kết Nối và Phản Hồi Khách Hàng"=> "Viết một status hiển thị phản hồi tích cực từ khách hàng, nhấn mạnh sự hài lòng của họ, mời khách hàng chia sẻ trải nghiệm để tăng kết nối.",
                            "Xây Dựng Cộng Đồng"=> "Tạo bài viết về 'Cộng đồng' của sản phẩm/dịch vụ, lợi ích mà cộng đồng mang lại, khuyến khích tham gia các hoạt động hoặc sự kiện, nhấn mạnh mối liên hệ với các mục tiêu xã hội.",
                            "Hướng Dẫn và Giáo Dục Khách Hàng"=> "Viết bài hướng dẫn sử dụng sản phẩm từ 'Cách sử dụng sản phẩm', cung cấp thông tin hữu ích và giải thích lợi ích khi sử dụng đúng cách.",
                            "Phân Tích Rào cản Khách Hàng"=> "Viết bài tự thống kê các rào cản mà khách hàng gặp phải, khuyến khích chia sẻ vướng mắc để hiểu thêm về nhu cầu.",
                            "Tạo Dựng Mối Quan Hệ Với Khách Hàng"=> "Viết bài chia sẻ phản hồi khách hàng, kể câu chuyện sử dụng sản phẩm, nhấn mạnh điểm mạnh trong dịch vụ hỗ trợ và khuyến khích tương tác.",
                            "Xu Hướng Và Tương Lai"=> "Viết status kết nối sản phẩm/dịch vụ với xu hướng hoặc sự kiện phổ biến, thể hiện tầm nhìn và mục tiêu của thương hiệu.",
                            "Tăng Cường Hình Ảnh Thương Hiệu"=> "Tạo một status thể hiện giá trị và văn hóa thương hiệu qua sản phẩm/dịch vụ, nhấn mạnh cam kết chất lượng, trách nhiệm xã hội và sự sáng tạo."
            ],
                'desired_emotion_mapping' => [
                    "Tin tưởng"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tin tưởng. Hãy viết bài quảng cáo tập trung vào việc xây dựng niềm tin, nhấn mạnh chứng nhận, đánh giá từ khách hàng và uy tín của sản phẩm.",
                                    "Hứng khởi"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hứng khởi. Hãy tạo bài viết làm nổi bật sự mới lạ, độc đáo của sản phẩm, khiến khách hàng cảm thấy hứng khởi và muốn khám phá ngay.",
                                    "Khẩn cấp"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khẩn cấp. Viết bài kèm thông điệp khẩn cấp, sử dụng ngôn ngữ nhấn mạnh ưu đãi có thời hạn hoặc số lượng giới hạn để thúc đẩy khách hàng hành động.",
                                    "Hạnh phúc"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là niềm hạnh phúc. Hãy viết bài mô tả cảm giác hạnh phúc và niềm vui mà khách hàng có thể trải nghiệm khi sử dụng sản phẩm/dịch vụ.",
                                    "Tự hào"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tự hào. Tạo bài viết nhấn mạnh giá trị đẳng cấp và lý do khách hàng sẽ cảm thấy tự hào khi sử dụng sản phẩm.",
                                    "Thấu hiểu"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự thấu hiểu. Hãy tạo bài viết thể hiện sự đồng cảm với khó khăn của khách hàng và cách sản phẩm/dịch vụ giúp họ giải quyết vấn đề.",
                                    "Khao khát"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự khao khát. Viết bài tập trung vào việc khơi gợi khao khát của khách hàng bằng cách nhấn mạnh giá trị và lợi ích độc đáo của sản phẩm.",
                                    "An tâm"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự an tâm. Tạo nội dung làm rõ các cam kết, bảo hành, và chứng minh tính an toàn, hiệu quả của sản phẩm.",
                                    "Truyền cảm hứng"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự cảm hứng. Hãy viết bài mang tính truyền cảm hứng, nhấn mạnh những thay đổi tích cực mà sản phẩm/dịch vụ có thể mang lại.",
                                    "Tò mò"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự tò mò. Viết bài mở đầu với một câu chuyện hoặc dữ liệu thú vị, làm khách hàng tò mò và muốn tìm hiểu chi tiết hơn.",
                                    "Yêu thương"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự yêu thương. Hãy tạo bài viết làm nổi bật cách sản phẩm/dịch vụ mang lại sự yêu thương, chăm sóc cho bản thân hoặc gia đình.",
                                    "Vui vẻ Hài hước"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hài hước. Hãy viết bài quảng cáo hài hước và dí dỏm để tạo sự thích thú và dễ dàng chia sẻ trên mạng xã hội.",
                                    "Động lực"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự động lực. Hãy viết bài quảng cáo mang tính khích lệ, tập trung vào việc thúc đẩy khách hàng thực hiện hành động cụ thể ngay lập tức.",
                                    "Hoài niệm"=> "Cảm xúc mong muốn đọng lại trong tâm trí khách hàng là sự hoài niệm. Viết bài quảng cáo gợi nhớ những kỷ niệm đẹp trong quá khứ và kết nối cảm xúc với sản phẩm/dịch vụ."
            ],
                'writing_style_mapping' => [
                        "Trang Trọng"=> "Phong cách viết: Tạo ra một phong cách trang trọng cho nội dung, phù hợp với môi trường kinh doanh chính thức hoặc các sự kiện quan trọng.",
                        "Hấp Dẫn và Sáng Tạo"=> "Phong cách viết: Làm cho nội dung trở nên sáng tạo và độc đáo, thu hút sự chú ý bằng cách thể hiện một cách tiếp cận mới lạ.",
                        "Thư Giãn và Thân Thiện"=> "Phong cách viết: Tạo nội dung để nó trở nên thân thiện và dễ chịu, tạo môi trường thư giãn cho người đọc.",
                        "Chuyên Nghiệp"=> "Phong cách viết: Tăng cường tính chuyên nghiệp và uy tín của nội dung, sử dụng ngôn ngữ chính xác và thông tin đáng tin cậy.",
                        "Hài hước và vui vẻ"=> "Phong cách viết: Thêm yếu tố hài hước và niềm vui vào nội dung, làm cho nó trở nên sinh động và giải trí.",
                        "Thể Thao và Năng Động"=> "Phong cách viết: Phản ánh tính năng động và sôi động, thích hợp với những sản phẩm liên quan đến thể thao hoặc hoạt động ngoài trời.",
                        "Hướng Dẫn và Giảng Dạy"=> "Phong cách viết: Đưa nội dung thành một hướng dẫn hoặc bài giảng, cung cấp thông tin hữu ích và kiến thức cho người đọc.",
                        "Chất Lượng và Tinh Tế"=> "Phong cách viết: Tăng cường chất lượng và tinh tế trong nội dung, nhấn mạnh sự chăm chút và độ chính xác của thông tin.",
                        "Ngắn Gọn và Súc Tích"=> "Phong cách viết: Làm cho nội dung trở nên ngắn gọn và đến trực tiếp điểm, loại bỏ mọi thông tin thừa.",
                        "Chia Sẻ Kinh Nghiệm Cá Nhân"=> "Phong cách viết: Thêm vào nội dung những trải nghiệm cá nhân hoặc câu chuyện từ người viết, làm cho nó trở nên thực tế và có liên quan hơn.",
                        "Truyện Cười và Giải Trí"=> "Phong cách viết: Biến nội dung thành một phần giải trí, thêm truyện cười hoặc yếu tố giải trí để thu hút người đọc.",
                        "Đánh Giá và So Sánh"=> "Phong cách viết: Thêm phần đánh giá hoặc so sánh sản phẩm/dịch vụ với các lựa chọn khác, cung cấp thông tin chi tiết và khách quan.",
                        "Cảm Xúc và Sâu Sắc"=> "Phong cách viết: Tạo nội dung với cảm xúc sâu sắc, sử dụng ngôn từ mô tả cảm xúc và trải nghiệm đa chiều.",
                        "Tone Nghiêm túc"=> "Phong cách viết: Nội dung nghiêm túc và uy tín, thích hợp cho các thông điệp quan trọng.",
                        "Tone Lạc quan"=> "Phong cách viết: Làm cho nội dung trở nên lạc quan và tích cực hơn, truyền đạt sự tự tin và khả năng của sản phẩm.",
                        "Tone Hấp Dẫn và Thú vị"=> "Phong cách viết: Nội dung hấp dẫn và thú vị, thu hút sự chú ý của người đọc.",
                        "Tone Thư Thái, nhẹ nhàng"=> "Phong cách viết: Nội dung tạo cảm giác thư thái và nhẹ nhàng, giúp người đọc cảm thấy thoải mái khi tiếp nhận thông tin."
                    ],
                    'post_format_mapping' => [
                                "Status ngắn"=> "Định dạng bài viết là: Status ngắn. Hãy viết một bài quảng cáo ngắn gọn, thu hút, truyền tải thông điệp chính trong 2-5 câu.",
                                "Bài viết dài"=> "Định dạng bài viết là: Bài viết dài. Hãy viết một bài quảng cáo chi tiết, kể câu chuyện liên quan đến sản phẩm/dịch vụ, làm nổi bật giá trị và khơi gợi cảm xúc của khách hàng.",
                                "Dạng Danh sách"=> "Định dạng bài viết là: Danh sách. Hãy viết một bài quảng cáo dạng danh sách, làm nổi bật [số lượng] lý do khách hàng nên chọn sản phẩm/dịch vụ này.",
                                "Câu chuyện kể"=> "Định dạng bài viết là: Câu chuyện. Hãy viết bài quảng cáo dưới dạng kể chuyện, bắt đầu bằng một tình huống thực tế để thu hút sự quan tâm và dẫn dắt đến giá trị của sản phẩm/dịch vụ.",
                                "Kịch bản Video quảng cáo"=> "Định dạng bài viết là: Video quảng cáo. Hãy viết kịch bản quảng cáo video dài [số giây] giây, làm nổi bật giá trị chính của sản phẩm/dịch vụ với thông điệp dễ nhớ và hấp dẫn.",
                                "Câu hỏi thảo luận"=> "Định dạng bài viết là: Câu hỏi thảo luận. Hãy viết bài quảng cáo dạng câu hỏi để thu hút bình luận của khách hàng, liên quan đến sản phẩm hoặc chủ đề họ quan tâm.",
                                "Trích dẫn"=> "Định dạng bài viết là: Trích dẫn. Hãy tạo một câu trích dẫn ấn tượng về sản phẩm/dịch vụ, ngắn gọn và dễ ghi nhớ.",
                                "Hướng dẫn sử dụng"=> "Định dạng bài viết là: Hướng dẫn sử dụng. Hãy viết bài quảng cáo dưới dạng hướng dẫn từng bước cách sử dụng sản phẩm/dịch vụ để đạt hiệu quả tốt nhất.",
                                "So sánh"=> "Định dạng bài viết là: So sánh. Hãy viết bài quảng cáo so sánh sản phẩm/dịch vụ này với các đối thủ, làm nổi bật các ưu điểm và sự khác biệt.",
                                "Feedback từ khách hàng"=> "Định dạng bài viết là: Feedback từ khách hàng. Hãy viết một bài quảng cáo dựa trên phản hồi của khách hàng, làm nổi bật trải nghiệm tích cực và lợi ích họ đạt được.",
                                "Câu chuyện khách hàng"=> "Định dạng bài viết là: Câu chuyện khách hàng. Hãy viết bài quảng cáo kể lại câu chuyện của một khách hàng sử dụng sản phẩm/dịch vụ, tập trung vào những thay đổi tích cực trong cuộc sống của họ.",
                                "Bài viết dựa trên số liệu"=> "Định dạng bài viết là: Bài viết dựa trên số liệu. Hãy viết bài quảng cáo tập trung vào các số liệu thống kê hoặc dữ liệu thực tế để chứng minh hiệu quả của sản phẩm/dịch vụ."
                            ],
                      'cta_mapping' => [
                                  "Mua Ngay"=> "Thêm CTA 'Mua Ngay' với liên kết trực tiếp đến trang mua hàng, kèm theo lợi ích cụ thể khi mua sản phẩm.",
                                  "Khuyến khích liên kết và tương tác"=> "Bổ sung CTA khuyến khích người đọc tương tác, như 'Bình luận ý kiến của bạn' hoặc 'Chia sẻ với bạn bè'.",
                                  "Đăng Ký tham gia"=> "Thêm lời kêu gọi đăng ký tham gia sự kiện hoặc nhận bản tin, với hướng dẫn cụ thể về cách thức đăng ký.",
                                  "Xem Chi Tiết"=> "CTA 'Xem Chi Tiết' với liên kết dẫn đến trang cung cấp thông tin chi tiết hơn về sản phẩm hoặc dịch vụ.",
                                  "Liên Hệ Chúng Tôi"=> "Thêm thông tin liên hệ như địa chỉ email, số điện thoại hoặc form liên hệ trực tuyến để khách hàng có thể liên hệ dễ dàng.",
                                  "Thêm Chia Sẻ"=> "Khuyến khích người đọc chia sẻ nội dung, có thể thông qua CTA như 'Chia sẻ nếu bạn thấy hữu ích'.",
                                  "Tăng bình luận"=> "Thêm CTA kích thích người đọc bình luận, như 'Hãy cho chúng tôi biết suy nghĩ của bạn về vấn đề này'."
                              ]
        ]
    ]
];
