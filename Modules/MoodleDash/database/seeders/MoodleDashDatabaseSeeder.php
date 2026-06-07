<?php

namespace Modules\MoodleDash\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\MoodleDash\Models\User;
use Modules\MoodleDash\Models\Course;
use Modules\MoodleDash\Models\Enrollment;
use Modules\MoodleDash\Models\Assignment;
use Modules\MoodleDash\Models\Submission;

class MoodleDashDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Teacher
        $teacher = User::create([
            'username' => 'prof_kim',
            'lastname' => 'Kim',
            'firstname' => 'Minsu',
            'email' => 'prof_kim@univ.ac.kr',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'userpictureurl' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
            'lastaccess' => time()
        ]);

        // 2. Create Student (Demo)
        $studentDemo = User::create([
            'username' => 'student_hong',
            'lastname' => 'Hong',
            'firstname' => 'Gildong',
            'email' => 'student_hong@univ.ac.kr',
            'password' => Hash::make('password'),
            'role' => 'student',
            'userpictureurl' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200',
            'lastaccess' => time()
        ]);

        // 3. Create Mock Students
        $mockStudentsInfo = [
            ['username' => 'student_lee', 'last' => 'Lee', 'first' => 'Jiwon', 'email' => 'jw.lee@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_park', 'last' => 'Park', 'first' => 'Minjun', 'email' => 'mj.park@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_hekim', 'last' => 'Kim', 'first' => 'Haeun', 'email' => 'he.kim@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_choi', 'last' => 'Choi', 'first' => 'Woojin', 'email' => 'wj.choi@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_yoon', 'last' => 'Yoon', 'first' => 'Seoyeon', 'email' => 'sy.yoon@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_jung', 'last' => 'Jung', 'first' => 'Dohyun', 'email' => 'dh.jung@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_han', 'last' => 'Han', 'first' => 'Mina', 'email' => 'ma.han@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_kang', 'last' => 'Kang', 'first' => 'Dongwoo', 'email' => 'dw.kang@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_cho', 'last' => 'Cho', 'first' => 'Sua', 'email' => 'sa.cho@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_song', 'last' => 'Song', 'first' => 'Jiho', 'email' => 'jh.song@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=150']
        ];

        $students = [];
        foreach ($mockStudentsInfo as $index => $sInfo) {
            $isRisk = ($index == 2) || ($index == 4) || ($index == 9);
            $lastAccessDiff = $isRisk ? (7 + $index * 2) : rand(0, 4);

            $students[] = User::create([
                'username' => $sInfo['username'],
                'lastname' => $sInfo['last'],
                'firstname' => $sInfo['first'],
                'email' => $sInfo['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'userpictureurl' => $sInfo['pic'],
                'lastaccess' => time() - $lastAccessDiff * 86400
            ]);
        }

        // 4. Create Courses
        $coursesInfo = [
            101 => [
                'fullname' => "CSE201: Data Structures",
                'shortname' => "Data Structures",
                'summary' => "Learn fundamental data structures such as lists, stacks, queues, trees, and graphs, along with algorithm analysis and implementation.",
                'topics' => json_encode(['Lists Basics', 'Stacks & Queues', 'Binary Trees', 'Graphs shortest path'])
            ],
            102 => [
                'fullname' => "CSE305: Introduction to AI",
                'shortname' => "Intro to AI",
                'summary' => "Introduction to the history of AI, classic search methods, machine learning, neural networks, and deep learning.",
                'topics' => json_encode(['Search Algorithms', 'Machine Learning Basics', 'Supervised Learning', 'Introduction to Neural Networks'])
            ],
            103 => [
                'fullname' => "CSE402: Web Systems Design",
                'shortname' => "Web Systems Design",
                'summary' => "Covers full-stack web framework architecture, REST API design, database integration, and deployment strategies.",
                'topics' => json_encode(['HTML/CSS Layouts', 'ExpressJS Backend', 'Database Modeling', 'Cloud Deployment'])
            ],
            // Additional Catalog Courses
            201 => [
                'fullname' => "CSE302: Algorithm Design & Analysis",
                'shortname' => "Algorithms",
                'summary' => "Covers algorithm design paradigms like greedy, divide-and-conquer, dynamic programming, and complexity analysis.",
                'topics' => json_encode(['Divide & Conquer', 'Dynamic Programming', 'NP-Completeness'])
            ],
            202 => [
                'fullname' => "CSE204: Database Systems",
                'shortname' => "Databases",
                'summary' => "Learn relational database modeling, SQL, schema normalization, transactions, and indexing.",
                'topics' => json_encode(['Relational Algebra', 'SQL Writing', 'Normalization Theory'])
            ],
            203 => [
                'fullname' => "CSE309: Computer Networks",
                'shortname' => "Networks",
                'summary' => "Covers TCP/IP protocols, socket programming, routing, switching, and network security basics.",
                'topics' => json_encode(['TCP/IP Layers', 'Socket Communication', 'Network Routing'])
            ]
        ];

        $courses = [];
        foreach ($coursesInfo as $id => $cVal) {
            $courses[$id] = Course::create([
                'id' => $id,
                'fullname' => $cVal['fullname'],
                'shortname' => $cVal['shortname'],
                'summary' => $cVal['summary'],
                'topics' => $cVal['topics']
            ]);
        }

        // 5. Create Enrollments
        // Enroll Demo Student in 101, 102, 103
        Enrollment::create(['user_id' => $studentDemo->id, 'course_id' => 101, 'progress' => 68, 'feedback' => 'Showing excellent learning participation.']);
        Enrollment::create(['user_id' => $studentDemo->id, 'course_id' => 102, 'progress' => 52, 'feedback' => 'High quality assignment submissions.']);
        Enrollment::create(['user_id' => $studentDemo->id, 'course_id' => 103, 'progress' => 74, 'feedback' => 'Outstanding web technology practical skills.']);

        // Enroll Mock Students
        foreach ($students as $index => $s) {
            foreach ([101, 102, 103] as $courseId) {
                // Determine risk parameters matching original scenarios
                $isRisk = ($courseId == 101 && $index == 2) || ($courseId == 102 && ($index == 4 || $index == 9));
                
                $progressVal = $courseId == 101 ? 68 : ($courseId == 102 ? 52 : 74);
                $progressVal += rand(-15, 15);
                if ($isRisk) {
                    $progressVal = max(15, $progressVal - 35);
                }
                $progressVal = min(100, max(0, $progressVal));

                Enrollment::create([
                    'user_id' => $s->id,
                    'course_id' => $courseId,
                    'progress' => $progressVal,
                    'feedback' => $isRisk 
                        ? 'No logins detected for over 7 days. Careful monitoring is recommended.' 
                        : 'Student is learning actively.'
                ]);
            }
        }

        // 6. Create Assignments
        $assignmentsInfo = [
            101 => [
                ['id' => 1001, 'name' => "Assignment 1: Linked List & Array List Analysis", 'deadline' => time() - 30 * 86400, 'maxgrade' => 100],
                ['id' => 1002, 'name' => "Assignment 2: Stack-based Calculator Implementation", 'deadline' => time() - 4 * 86400, 'maxgrade' => 100],
                ['id' => 1003, 'name' => "Assignment 3: Binary Search Tree Optimization", 'deadline' => time() + 8 * 86400, 'maxgrade' => 100]
            ],
            102 => [
                ['id' => 1004, 'name' => "Lab 1: BFS/DFS Maze Solving Algorithm", 'deadline' => time() - 20 * 86400, 'maxgrade' => 100],
                ['id' => 1005, 'name' => "Project 1: Housing Price Prediction with Scikit-learn", 'deadline' => time() - 2 * 86400, 'maxgrade' => 100],
                ['id' => 1006, 'name' => "Project 2: CNN Image Classifier using PyTorch/TensorFlow", 'deadline' => time() + 15 * 86400, 'maxgrade' => 100]
            ],
            103 => [
                ['id' => 1007, 'name' => "Assignment 1: Responsive Portfolio Page with HTML5/CSS3", 'deadline' => time() - 15 * 86400, 'maxgrade' => 50],
                ['id' => 1008, 'name' => "Assignment 2: REST API Backend with ExpressJS", 'deadline' => time() + 3 * 86400, 'maxgrade' => 100]
            ]
        ];

        $assignments = [];
        foreach ($assignmentsInfo as $courseId => $aList) {
            foreach ($aList as $a) {
                $assignments[] = Assignment::create([
                    'id' => $a['id'],
                    'course_id' => $courseId,
                    'name' => $a['name'],
                    'deadline' => $a['deadline'],
                    'maxgrade' => $a['maxgrade']
                ]);
            }
        }

        // 7. Create Submissions
        // Seed submissions for Demo Student (userid: 2)
        foreach ($assignments as $a) {
            $isPast = $a->deadline < time();
            $status = $isPast ? 'submitted' : 'new';
            $submitTime = $status === 'submitted' ? $a->deadline - 50000 : 0;
            $grade = ($status === 'submitted' && $isPast) ? intval($a->maxgrade * 0.85) : null;

            Submission::create([
                'assignment_id' => $a->id,
                'user_id' => $studentDemo->id,
                'status' => $status,
                'grade' => $grade,
                'timemodified' => $submitTime
            ]);
        }

        // Seed submissions for Mock Students
        foreach ($students as $index => $s) {
            foreach ($assignments as $a) {
                // Determine if this student is warning risk
                $isRisk = ($a->course_id == 101 && $index == 2) || ($a->course_id == 102 && ($index == 4 || $index == 9));
                $isPast = $a->deadline < time();

                if ($isRisk && (rand(0, 10) > 5)) {
                    // Risk student occasionally misses homework completely
                    if ($isPast) {
                        Submission::create([
                            'assignment_id' => $a->id,
                            'user_id' => $s->id,
                            'status' => 'new',
                            'grade' => null,
                            'timemodified' => 0
                        ]);
                    }
                    continue;
                }

                $status = 'submitted';
                $submitTime = $a->deadline - rand(1000, 200000);
                
                if (!$isPast && (rand(0, 10) > 4)) {
                    $status = 'new';
                    $submitTime = 0;
                }

                $grade = null;
                if ($status === 'submitted' && $isPast) {
                    // Random grade based on progress
                    $progressVal = 68; // default
                    $enroll = Enrollment::where('user_id', $s->id)->where('course_id', $a->course_id)->first();
                    if ($enroll) {
                        $progressVal = $enroll->progress;
                    }
                    $perf = $progressVal / 100.0;
                    $grade = intval($a->maxgrade * (0.6 + $perf * 0.35 + rand(0, 5) / 100.0));
                }

                Submission::create([
                    'assignment_id' => $a->id,
                    'user_id' => $s->id,
                    'status' => $status,
                    'grade' => $grade,
                    'timemodified' => $submitTime
                ]);
            }
        }
    }
}
