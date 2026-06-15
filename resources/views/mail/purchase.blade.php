<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Thành Công</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-body {
            padding: 20px;
        }
        .email-body p {
            margin: 10px 0;
        }
        .email-footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777;
        }
        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .highlight {
            color: #007BFF;
            font-weight: bold;
        }
        .account-info {
            border: 2px solid #007BFF;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            background-color: #f0f8ff;
        }
        .account-info-title {
            font-size: 18px;
            font-weight: bold;
            color: #007BFF;
            margin-bottom: 10px;
            text-align: center;
        }
        .account-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Thanh Toán Thành Công!</h1>
        </div>
        <div class="email-body">
            <p>Kính gửi <span class="highlight">{{ $name }}</span>,</p>

            <p>Chúc mừng! Chúng tôi rất vui mừng thông báo rằng bạn đã thanh toán thành công cho nền tảng <strong>{{ $appName }}</strong> của chúng tôi.</p>

            <div class="account-info">
                <div class="account-info-title">Thông Tin Tài Khoản</div>
                <p><strong>Tên đăng nhập:</strong> {{ $email }}</p>
                <p><strong>Mật khẩu:</strong> {{ $password }}</p>
                <p><a href="{{ $redirectLoginUrl }}">Đăng nhập tại đây</a></p>
            </div>

            <p>Sau khi đăng nhập, bạn có thể truy cập và sử dụng đầy đủ các tính năng của <strong>{{ $appName }}</strong> theo nhu cầu của mình.</p>

            <p>Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, vui lòng liên hệ với đội ngũ hỗ trợ khách hàng của chúng tôi qua email này.</p>

            <p>Một lần nữa, chúng tôi chân thành cảm ơn bạn đã lựa chọn dịch vụ của chúng tôi. Chúc bạn có một trải nghiệm tuyệt vời!</p>
        </div>
    </div>
</body>
</html>
