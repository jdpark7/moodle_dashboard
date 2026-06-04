<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 40px 20px;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e7ff;
        }
        .header {
            background: linear-gradient(135deg, #4f66ff 0%, #6366f1 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.8;
            font-size: 15px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LMS 학습 모니터링 안내</h1>
        </div>
        <div class="content">
            {!! nl2br(e($aiMessage)) !!}
        </div>
        <div class="footer">
            본 메일은 {{ $courseName }} 강좌의 학습 현황에 근거하여 자동으로 발송되었습니다.<br>
            © Antigravity University LMS Board
        </div>
    </div>
</body>
</html>
