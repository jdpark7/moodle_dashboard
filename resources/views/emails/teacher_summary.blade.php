<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 40px 20px;
            color: #334155;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: #1e293b;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .stats-box {
            background-color: #f1f5f9;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-item h4 {
            margin: 0;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
        }
        .stats-item p {
            margin: 5px 0 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
        }
        .reason-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            background-color: #fef2f2;
            color: #ef4444;
        }
        .message-preview {
            color: #64748b;
            font-style: italic;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LMS 학업 독려 이메일 발송 현황 보고</h1>
        </div>
        <div class="content">
            <p>교수자님 안녕하십니까,</p>
            <p><strong>{{ $courseName }}</strong> 강좌의 수강생 모니터링 분석에 기반한 학업 독려 이메일 발송 결과 요약본입니다.</p>
            
            <div class="stats-box">
                <div class="stats-item">
                    <h4>과목명</h4>
                    <p>{{ $courseName }}</p>
                </div>
                <div class="stats-item">
                    <h4>발송 대상자</h4>
                    <p>{{ $sentCount }}명</p>
                </div>
                <div class="stats-item">
                    <h4>상태</h4>
                    <p>발송 완료 (Log)</p>
                </div>
            </div>

            <h3>발송 대상자 목록</h3>
            <table>
                <thead>
                    <tr>
                        <th>학생 정보</th>
                        <th>유형</th>
                        <th>독려 사유</th>
                        <th>메시지 요약</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($logs))
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <strong>{{ $log['name'] }}</strong><br>
                                    <span style="font-size: 10px; color:#94a3b8;">{{ $log['email'] }}</span>
                                </td>
                                <td>
                                    <span class="reason-badge">{{ $log['type'] }}</span>
                                </td>
                                <td>{{ $log['reason'] }}</td>
                                <td class="message-preview" title="{{ $log['message'] }}">
                                    {{ Str::limit($log['message'], 40) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px;">
                                금일 이메일 발송이 필요한 경고 대상 학생이 없습니다.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
