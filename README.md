# Moodle LMS Dashboard (Laravel Version)

Moodle Web Service(REST API)를 백엔드로 연동하여 학생들에게는 수강 신청 및 과제 추적 기능을 제공하고, 교수자에게는 학업 진도율 분석 및 AI를 통한 개별 학습 독려 이메일 발송 기능을 제공하는 풀스택 대시보드 웹 애플리케이션입니다.

---

## 🚀 주요 기능 (Features)

### 👨‍🎓 학생 포털 (Student Portal)
* **수강 강좌 및 진도율**: 현재 수강 신청한 과목 리스트와 개별 이수 진행 상태(%)를 시각화된 프로그레스 바로 제공합니다.
* **통합 과제 관리**: 전체 수강 과목의 미제출 과제를 마감 기한이 임박한 순서대로 실시간 카운트다운 타이머와 함께 모아 보여줍니다.
* **수강신청 시뮬레이터**: Moodle 강좌 목록 중 미수강 강좌를 조회하여 실시간으로 수강 신청을 처리합니다.
* **수강 이력 관리**: 이전 학기 또는 이미 100% 이수한 완료 강좌들을 타임라인 형태로 표시합니다.

### 👨‍🏫 교수자 분석 포털 (Teacher Portal)
* **과목별 학습 분석 대시보드**: 총 수강생 수, 이수 완료도 평균, 미채점 과제 건수를 메트릭 카드로 한눈에 모니터링합니다.
* **데이터 시각화 (Chart.js)**:
  * 주간 학생 로그인 접속 횟수 추이 (Line Chart)
  * 학급 전체의 예상 평가 성적 분포도 (Bar Chart)
* **주의 대상자(위험군) 경보**: 7일 이상 로그인 기록이 없거나 진도율이 45% 미만인 취약 학생을 실시간 분류해 줍니다.
* **학생 분석 Roster**: 검색 및 상세 진단 기능을 포함한 수강생 목록 테이블을 제공합니다.
* **1:1 상세 진료 모달**: 학생별 세부 과제/퀴즈 점수를 조회하고, 지도 소견 피드백을 저장 및 실시간 업데이트합니다.
* **AI 독려 메일 발송**: 
  * 주의 대상 학생에게 **구글 Gemini AI**를 활용해 학생의 이름, 결석 일수 또는 미제출 과제 이름을 분석한 맞춤형 격려 메일을 자동 생성합니다.
  * 한 번의 클릭으로 학생에게 발송하고 교수자에게 요약 보고서 이메일을 발송합니다.

---

## 🛠️ 기술 스택 (Tech Stack)
* **Backend**: Laravel 11 (PHP 8.2+)
* **Frontend**: HTML5, Blade Template, Tailwind CSS (CDN), Lucide Icons, Chart.js
* **AI API**: Google Gemini 2.5 Flash Model
* **Database**: SQLite (기본 세션 및 연동 데이터 캐싱)

---

## 💻 설치 및 구동 방법 (Setup Guide)

### 1. 프로젝트 다운로드 및 이동
```bash
cd C:\Users\USER\moodledashboard-laravel
```

### 2. 패키지 설치
Composer 의존성 패키지를 로컬 환경에 맞게 다운로드합니다.
```bash
composer install
```

### 3. 환경 설정 (.env)
기존에 생성되어 있는 `.env` 파일을 확인하고, 구글 Gemini API 연동이 필요한 경우 키값을 등록합니다.
```env
# Gemini API 설정
GEMINI_API_KEY=your_google_gemini_api_key_here

# 메일 테스트 설정 (보안상 개발 환경에서는 이메일을 파일에 로그로 남깁니다)
MAIL_MAILER=log
```

### 4. 고유 암호화 키 생성
```bash
php artisan key:generate
```

### 5. 로컬 개발 서버 기동
```bash
php artisan serve
```
서버가 켜지면 브라우저에서 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**에 접속합니다.

---

## ⚙️ AI 자동화 및 메일링 검증

### 1. 대시보드 내 즉시 전송
교수자 대시보드 화면 내 **"AI 독려 메일 발송"** 버튼을 클릭하면, 실시간으로 AI 문구가 생성되어 백엔드에서 메일이 발송되고 결과 보고서가 화면에 팝업됩니다.

### 2. Daily Cron 스케줄러 (Artisan 배치)
매일 정해진 시간에 자동으로 미접속/미제출 자를 추적해 이메일을 발송하도록 스케줄러가 [routes/console.php](routes/console.php)에 등록되어 있습니다.
터미널에서 수동으로 스케줄링 명령을 테스트해 보려면 아래와 같이 실행합니다.
```bash
php artisan moodle:send-encouragement
```

### 3. 발송된 이메일 로그 확인
본 프로젝트는 보안을 위해 SMTP 연동 대신 `MAIL_MAILER=log` 방식을 취하고 있어, 발송된 격려 메일 및 요약 보고서 메일이 실제로 발송되는 대신 아래 경로에 파일 로그로 상세히 기록됩니다:
* **`storage/logs/laravel.log`**
