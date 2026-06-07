# Moodle LMS Dashboard (Laravel Version)

A full-stack dashboard web application that integrates the Moodle Web Service (REST API) as a backend. It provides students with course registration and assignment tracking features, while equipping teachers with academic progress analytics and automated individual encouragement email dispatch powered by AI.

---

## 🚀 Key Features

### 👨‍🎓 Student Portal
* **Enrolled Courses & Progress**: Displays currently enrolled courses with their individual completion progress (%) visualized as sleek progress bars.
* **Integrated Assignment Management**: Aggregates all pending assignments across enrolled courses, sorted by deadline, with real-time remaining time tracking.
* **Course Enrollment Simulator**: Allows students to browse available Moodle courses they haven't enrolled in yet and register in real-time.
* **Course History Management**: Displays completed or past semester courses in a timeline layout.

### 👨‍🏫 Teacher Analytics Portal
* **Course Analytics Dashboard**: Monitor student counts, average completion progress, and ungraded assignment counts at a glance with metric cards.
* **Data Visualization (Chart.js)**:
  * Weekly student login trends (Line Chart)
  * Grade distribution prediction for the entire class (Bar Chart)
* **At-Risk Student Alerts**: Automatically identifies and categorizes high-risk students who have not logged in for over 7 days or have a completion progress below 45%.
* **Student Roster**: Interactive student listing with search and detailed academic diagnostics.
* **1:1 Academic Diagnosis Modal**: View detailed assignment and quiz scores for individual students, and save or update advising comments in real-time.
* **AI Encouragement Mailer**: 
  * Generates personalized encouragement emails using **Google Gemini AI** by analyzing the student's name, consecutive days absent, or names of missing assignments.
  * Sends the generated email to the student and emails a summary report to the teacher with a single click.

---

## 🛠️ Tech Stack
* **Backend**: Laravel 12 (PHP 8.2+)
* **Frontend**: HTML5, Blade Templates, Tailwind CSS (CDN), Lucide Icons, Chart.js
* **AI API**: Google Gemini 2.5 Flash Model
* **Database**: SQLite / MariaDB (Handles session data and connection caching)

---

## 📁 Modular Architecture
This project uses the **`nwidart/laravel-modules`** package to modularize dashboard components, resembling Django's app structure. All routes, controllers, services, mails, views, and console commands are encapsulated inside the `Modules/MoodleDash` directory.

```
Modules/
└── MoodleDash/
    ├── app/
    │   ├── Console/
    │   │   └── MoodleSendEncouragement.php (Artisan Command)
    │   ├── Http/
    │   │   └── Controllers/
    │   │       ├── Controller.php (Base Controller)
    │   │       ├── LoginController.php
    │   │       ├── StudentController.php
    │   │       └── TeacherController.php
    │   ├── Mail/
    │   │   ├── EncouragementMail.php
    │   │   └── TeacherSummaryMail.php
    │   ├── Providers/
    │   │   └── MoodleDashServiceProvider.php (Module Service Provider)
    │   └── Services/
    │       ├── AiMessageService.php
    │       ├── MockMoodleService.php
    │       └── MoodleService.php
    ├── module.json (Module Metadata)
    ├── resources/
    │   └── views/ (Blade templates referenced via 'moodledash::')
    │       ├── emails/
    │       ├── layouts/
    │       ├── login.blade.php
    │       ├── student_dashboard.blade.php
    │       └── teacher_dashboard.blade.php
    └── routes/
        └── web.php (Module routes loaded under 'web' middleware group)
```

---

## 💻 Setup Guide

### 1. Clone & Navigate to Project Directory
```bash
cd C:\Users\USER\moodledashboard-laravel
```

### 2. Install Packages & Autoload Configuration
Download the required Composer packages and bind the module namespace (`Modules\`).
```bash
composer install
composer dump-autoload
```

### 3. Environment Configuration (`.env`)
Configure your `.env` file. If you wish to enable Google Gemini AI features, configure your API key.
```env
# Gemini API Configuration
GEMINI_API_KEY=your_google_gemini_api_key_here

# Mail Testing Configuration (Saves emails as files in development)
MAIL_MAILER=log
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run the Local Development Server
```bash
php artisan serve
```
Once the server is running, open your browser and navigate to **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

---

## ⚙️ AI Automation & Email Verification

### 1. Instant Dispatch from Dashboard
Clicking the **"Send AI Encouragement Mail"** button on the teacher dashboard generates a customized AI message in real-time, dispatches the email in the background, and displays a summary report pop-up.

### 2. Daily Cron Scheduler (Artisan Batch)
A cron schedule is registered in [routes/console.php](routes/console.php) to automatically track inactive students or pending assignments and send emails at specified times.
To manually test the scheduling command in your terminal, run:
```bash
php artisan moodle:send-encouragement
```

### 3. Check Sent Email Logs
For safety and testing purposes, the application defaults to `MAIL_MAILER=log`. Dispatched student encouragement and teacher summary emails will be recorded in detail inside the local log file instead of sending out real emails:
* **`storage/logs/laravel.log`**
