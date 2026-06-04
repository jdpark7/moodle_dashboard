<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class MoodleService
{
    protected $baseUrl;
    protected $token;

    public function __construct($baseUrl, $token = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
    }

    /**
     * Fetch Moodle web service token using username/password connection
     */
    public static function getToken($baseUrl, $username, $password, $service = 'moodle_mobile_app')
    {
        $cleanUrl = rtrim($baseUrl, '/');
        $url = $cleanUrl . '/login/token.php';
        
        try {
            $response = Http::timeout(10)->get($url, [
                'username' => $username,
                'password' => $password,
                'service' => $service
            ]);

            if ($response->failed()) {
                throw new Exception("HTTP request failed with status " . $response->status());
            }

            $data = $response->json();
            if (isset($data['error'])) {
                throw new Exception($data['error']);
            }

            return $data['token'] ?? null;
        } catch (Exception $e) {
            logger()->error("Moodle getToken failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Executes a Moodle Web Service function call
     */
    public function call($wsfunction, $params = [])
    {
        if (!$this->token) {
            throw new Exception("API Token is required to call Moodle Web Services.");
        }

        $url = $this->baseUrl . '/webservice/rest/server.php';
        
        $queryParams = [
            'wstoken' => $this->token,
            'wsfunction' => $wsfunction,
            'moodlewsrestformat' => 'json'
        ];

        try {
            // Moodle REST APIs expect application/x-www-form-urlencoded params.
            // http_build_query naturally handles nested arrays in PHP format.
            $response = Http::asForm()
                ->timeout(15)
                ->post($url . '?' . http_build_query($queryParams), $params);

            if ($response->failed()) {
                throw new Exception("HTTP request to Moodle failed: " . $response->status());
            }

            $data = $response->json();

            // Check for Moodle's internal errors returned as 200 JSON
            if (is_array($data) && isset($data['exception'])) {
                throw new Exception($data['message'] ?? $data['exception']);
            }

            return $data;
        } catch (Exception $e) {
            logger()->error("Moodle API call failed [{$wsfunction}]: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSiteInfo()
    {
        return $this->call('core_webservice_get_site_info');
    }

    public function getCourses($userid)
    {
        return $this->call('core_enrol_get_users_courses', ['userid' => $userid]);
    }

    public function getEnrolledUsers($courseid)
    {
        return $this->call('core_enrol_get_enrolled_users', ['courseid' => $courseid]);
    }

    public function getAssignments($courseids)
    {
        return $this->call('mod_assign_get_assignments', ['courseids' => $courseids]);
    }

    public function getSubmissions($assignmentids)
    {
        return $this->call('mod_assign_get_submissions', ['assignmentids' => $assignmentids]);
    }

    public function getQuizzes($courseids)
    {
        return $this->call('mod_quiz_get_quizzes_by_courses', ['courseids' => $courseids]);
    }

    public function getGradeItems($courseid)
    {
        return $this->call('gradereport_user_get_grade_items', ['courseid' => $courseid]);
    }

    public function selfEnrolUser($courseid)
    {
        return $this->call('enrol_self_enrol_user', ['courseid' => $courseid]);
    }
}
