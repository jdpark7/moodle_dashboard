<?php

namespace Modules\MoodleDash\Services;

class MockMoodleService
{
    protected $teacherInfo;
    protected $studentInfo;
    protected $allCourses;
    protected $mockStudentTemplates;
    protected $assignments;
    protected $quizzes;

    public function __construct()
    {
        // Initialize default student state in session if not present
        if (!session()->has('student_courses')) {
            session(['student_courses' => [101, 102, 103]]);
        }
        if (!session()->has('student_history')) {
            session(['student_history' => [98, 99]]);
        }

        $this->teacherInfo = [
            'sitename' => "Antigravity University LMS",
            'username' => "prof_kim",
            'firstname' => "Minsu",
            'lastname' => "Kim",
            'fullname' => "Prof. Minsu Kim",
            'userid' => 99,
            'userpictureurl' => "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200"
        ];

        $this->studentInfo = [
            'sitename' => "Antigravity University LMS",
            'username' => "student_hong",
            'firstname' => "Gildong",
            'lastname' => "Hong",
            'fullname' => "Gildong Hong",
            'userid' => 1001,
            'userpictureurl' => "https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200"
        ];

        $this->allCourses = [
            101 => [
                'id' => 101,
                'fullname' => "CSE201: Data Structures",
                'shortname' => "Data Structures",
                'summary' => "Learn fundamental data structures such as lists, stacks, queues, trees, and graphs, along with algorithm analysis and implementation.",
                'startdate' => time() - 90 * 86400,
                'enrolledusercount' => 32,
                'progress' => 68
            ],
            102 => [
                'id' => 102,
                'fullname' => "CSE305: Introduction to AI",
                'shortname' => "Intro to AI",
                'summary' => "Introduction to the history of AI, classic search methods, machine learning, neural networks, and deep learning.",
                'startdate' => time() - 90 * 86400,
                'enrolledusercount' => 45,
                'progress' => 52
            ],
            103 => [
                'id' => 103,
                'fullname' => "CSE402: Web Systems Design",
                'shortname' => "Web Systems Design",
                'summary' => "Covers full-stack web framework architecture, REST API design, database integration, and deployment strategies.",
                'startdate' => time() - 90 * 86400,
                'enrolledusercount' => 24,
                'progress' => 74
            ],
            201 => [
                'id' => 201,
                'fullname' => "CSE302: Algorithm Design & Analysis",
                'shortname' => "Algorithms",
                'summary' => "Covers algorithm design paradigms like greedy, divide-and-conquer, dynamic programming, and complexity analysis.",
                'startdate' => time() + 10 * 86400,
                'enrolledusercount' => 0,
                'progress' => 0
            ],
            202 => [
                'id' => 202,
                'fullname' => "CSE204: Database Systems",
                'shortname' => "Databases",
                'summary' => "Learn relational database modeling, SQL, schema normalization, transactions, and indexing.",
                'startdate' => time() + 15 * 86400,
                'enrolledusercount' => 0,
                'progress' => 0
            ],
            203 => [
                'id' => 203,
                'fullname' => "CSE309: Computer Networks",
                'shortname' => "Networks",
                'summary' => "Covers TCP/IP protocols, socket programming, routing, switching, and network security basics.",
                'startdate' => time() + 20 * 86400,
                'enrolledusercount' => 0,
                'progress' => 0
            ],
            // Past Courses
            99 => [
                'id' => 99,
                'fullname' => "CSE101: Introduction to Programming",
                'shortname' => "Intro to Programming",
                'summary' => "Learn basic programming logic using Python, covering variables, loops, conditionals, and functions.",
                'startdate' => time() - 200 * 86400,
                'enrolledusercount' => 50,
                'progress' => 100
            ],
            98 => [
                'id' => 98,
                'fullname' => "MATH103: Discrete Mathematics",
                'shortname' => "Discrete Math",
                'summary' => "Build mathematical foundations for computer science, covering logic, sets, matrices, and graph theory.",
                'startdate' => time() - 200 * 86400,
                'enrolledusercount' => 42,
                'progress' => 100
            ]
        ];

        $this->mockStudentTemplates = [
            ['id' => 2001, 'name' => "Jiwon Lee", 'email' => "jw.lee@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2002, 'name' => "Minjun Park", 'email' => "mj.park@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2003, 'name' => "Haeun Kim", 'email' => "he.kim@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2004, 'name' => "Woojin Choi", 'email' => "wj.choi@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2005, 'name' => "Seoyeon Yoon", 'email' => "sy.yoon@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2006, 'name' => "Dohyun Jung", 'email' => "dh.jung@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2007, 'name' => "Mina Han", 'email' => "ma.han@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2008, 'name' => "Dongwoo Kang", 'email' => "dw.kang@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1552058544-f2b08422138a?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2009, 'name' => "Sua Cho", 'email' => "sa.cho@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2010, 'name' => "Jiho Song", 'email' => "jh.song@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=150"]
        ];

        $this->assignments = [
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
            ],
            201 => [
                ['id' => 1009, 'name' => "Assignment 1: Closest Pair Algorithm using Divide & Conquer", 'deadline' => time() + 20 * 86400, 'maxgrade' => 100]
            ],
            202 => [
                ['id' => 1010, 'name' => "Assignment 1: University DB Schema & ER Modeling", 'deadline' => time() + 25 * 86400, 'maxgrade' => 100]
            ],
            203 => [
                ['id' => 1011, 'name' => "Lab 1: Multi-threaded Chat Server with Python Socket API", 'deadline' => time() + 30 * 86400, 'maxgrade' => 100]
            ]
        ];

        $this->quizzes = [
            101 => [['id' => 801, 'name' => "Data Structures Midterm Quiz (Important)", 'timeclose' => time() - 15 * 86400, 'sumgrades' => 20]],
            102 => [['id' => 802, 'name' => "Machine Learning Basics Quiz", 'timeclose' => time() - 25 * 86400, 'sumgrades' => 15]],
            103 => [['id' => 803, 'name' => "JavaScript DOM Manipulation Quiz", 'timeclose' => time() - 10 * 86400, 'sumgrades' => 30]],
            201 => [],
            202 => [],
            203 => []
        ];

        // Seed mock students DB into Laravel session to allow persistence
        if (!session()->has('students_db')) {
            $studentsDb = [];
            foreach ([101, 102, 103] as $courseId) {
                $roster = [];
                foreach ($this->mockStudentTemplates as $index => $t) {
                    $isRisk = ($courseId == 101 && $index == 2) || ($courseId == 102 && ($index == 4 || $index == 9));
                    $lastAccessDiff = $isRisk ? (7 + $index * 2) : rand(0, 4);
                    
                    $progressVal = $courseId == 101 ? 68 : ($courseId == 102 ? 52 : 74);
                    $progressVal += rand(-15, 15);
                    if ($isRisk) {
                        $progressVal = max(15, $progressVal - 35);
                    }
                    $progressVal = min(100, max(0, $progressVal));

                    $roster[] = [
                        'id' => $t['id'],
                        'name' => $t['name'],
                        'email' => $t['email'],
                        'pic' => $t['pic'],
                        'lastaccess' => time() - $lastAccessDiff * 86400,
                        'progress' => $progressVal,
                        'isRisk' => $isRisk,
                        'grade' => intval($progressVal * 0.9 + rand(0, 8)),
                        'feedback' => $isRisk ? "No logins detected for over 7 days. Careful monitoring is recommended." : "Student is learning actively.",
                    ];
                }
                $studentsDb[$courseId] = $roster;
            }
            session(['students_db' => $studentsDb]);
        }
    }

    public function getSiteInfo($role = 'student')
    {
        return $role === 'teacher' ? $this->teacherInfo : $this->studentInfo;
    }

    public function getCourses($userid = null, $role = 'student')
    {
        if ($role === 'teacher') {
            return array_map(function($cid) {
                return $this->allCourses[$cid];
            }, [101, 102, 103]);
        }

        $enrolledIds = session('student_courses', [101, 102, 103]);
        return array_map(function($cid) {
            return $this->allCourses[$cid];
        }, $enrolledIds);
    }

    public function getHistoryCourses()
    {
        $historyIds = session('student_history', [98, 99]);
        return array_map(function($cid) {
            return $this->allCourses[$cid];
        }, $historyIds);
    }

    public function getCatalogCourses()
    {
        $enrolled = session('student_courses', [101, 102, 103]);
        $history = session('student_history', [98, 99]);
        
        $diff = array_diff([201, 202, 203], $enrolled, $history);
        
        return array_map(function($cid) {
            return $this->allCourses[$cid];
        }, $diff);
    }

    public function selfEnrolUser($courseid)
    {
        $courseid = intval($courseid);
        $enrolled = session('student_courses', []);
        
        if (isset($this->allCourses[$courseid]) && !in_array($courseid, $enrolled)) {
            $enrolled[] = $courseid;
            session(['student_courses' => $enrolled]);
            
            // Seed blank roster space in students DB
            $studentsDb = session('students_db', []);
            if (!isset($studentsDb[$courseid])) {
                $studentsDb[$courseid] = [];
                session(['students_db' => $studentsDb]);
            }
            return ['status' => true];
        }
        return ['status' => false, 'message' => 'Course not found or already enrolled.'];
    }

    public function getEnrolledUsers($courseid)
    {
        $courseid = intval($courseid);
        $studentsDb = session('students_db', []);
        $students = $studentsDb[$courseid] ?? [];
        
        $moodleFormatted = [];
        foreach ($students as $s) {
            $first = mb_substr($s['name'], 1);
            $last = mb_substr($s['name'], 0, 1);
            
            $moodleFormatted[] = [
                'id' => $s['id'],
                'username' => 'student_' . $s['id'],
                'firstname' => $first,
                'lastname' => $last,
                'fullname' => $s['name'],
                'email' => $s['email'],
                'profileimageurl' => $s['pic'],
                'lastaccess' => $s['lastaccess'],
                'progress' => $s['progress'],
                'isRisk' => $s['isRisk'],
                'grade' => $s['grade'],
                'feedback' => $s['feedback']
            ];
        }
        return $moodleFormatted;
    }

    public function getAssignments($courseids)
    {
        $result = ['courses' => []];
        foreach ($courseids as $cid) {
            $cid = intval($cid);
            if (isset($this->assignments[$cid])) {
                $result['courses'][] = [
                    'id' => $cid,
                    'assignments' => $this->assignments[$cid]
                ];
            }
        }
        return $result;
    }

    public function getSubmissions($assignmentids)
    {
        $result = ['assignments' => []];
        $studentsDb = session('students_db', []);
        
        foreach ($assignmentids as $aid) {
            $aid = intval($aid);
            
            $targetCid = null;
            $targetAssign = null;
            foreach ($this->assignments as $cid => $assigns) {
                foreach ($assigns as $a) {
                    if ($a['id'] === $aid) {
                        $targetCid = $cid;
                        $targetAssign = $a;
                        break 2;
                    }
                }
            }

            if ($targetCid) {
                $subs = [];
                $isPast = $targetAssign['deadline'] < time();
                $students = $studentsDb[$targetCid] ?? [];

                foreach ($students as $index => $s) {
                    if ($s['isRisk'] && (rand(0, 10) > 5)) {
                        if ($isPast) {
                            $subs[] = [
                                'id' => 5000 + $aid + $s['id'],
                                'assignment' => $aid,
                                'userid' => $s['id'],
                                'status' => 'new',
                                'timemodified' => 0,
                                'grade' => null
                            ];
                        }
                        continue;
                    }

                    $submitStatus = 'submitted';
                    $submitTime = $targetAssign['deadline'] - rand(1000, 200000);
                    
                    if (!$isPast && (rand(0, 10) > 4)) {
                        $submitStatus = 'new';
                        $submitTime = 0;
                    }

                    $grade = null;
                    if ($submitStatus === 'submitted' && $isPast) {
                        $perf = $s['progress'] / 100.0;
                        $grade = intval($targetAssign['maxgrade'] * (0.6 + $perf * 0.35 + rand(0, 5) / 100.0));
                    }

                    $subs[] = [
                        'id' => 5000 + $aid + $s['id'],
                        'assignment' => $aid,
                        'userid' => $s['id'],
                        'status' => $submitStatus,
                        'timemodified' => $submitTime,
                        'grade' => $grade
                    ];
                }

                // Add Student Demo itself (userid: 1001)
                $submitStatus = $isPast || (rand(0, 10) > 2) ? 'submitted' : 'new';
                $submitTime = $submitStatus === 'submitted' ? $targetAssign['deadline'] - 50000 : 0;
                $grade = ($submitStatus === 'submitted' && $isPast) ? intval($targetAssign['maxgrade'] * 0.85) : null;

                $subs[] = [
                    'id' => 5000 + $aid + 1001,
                    'assignment' => $aid,
                    'userid' => 1001,
                    'status' => $submitStatus,
                    'timemodified' => $submitTime,
                    'grade' => $grade
                ];

                $result['assignments'][] = [
                    'assignment' => $aid,
                    'submissions' => $subs
                ];
            }
        }
        return $result;
    }

    public function getQuizzes($courseids)
    {
        $result = [];
        foreach ($courseids as $cid) {
            $cid = intval($cid);
            if (isset($this->quizzes[$cid])) {
                foreach ($this->quizzes[$cid] as $q) {
                    $result[] = array_merge($q, ['course' => $cid]);
                }
            }
        }
        return ['quizzes' => $result];
    }

    public function getGradeItems($courseid)
    {
        $courseid = intval($courseid);
        $studentsDb = session('students_db', []);
        $students = $studentsDb[$courseid] ?? [];
        $assigns = $this->assignments[$courseid] ?? [];
        $quizzes = $this->quizzes[$courseid] ?? [];

        $usergrades = [];
        foreach ($students as $s) {
            $gradeitems = [];
            foreach ($assigns as $a) {
                $isPast = $a['deadline'] < time();
                $score = $isPast ? intval($a['maxgrade'] * ($s['progress'] / 110.0 + rand(0, 10) / 100.0)) : null;
                $gradeitems[] = [
                    'itemname' => $a['name'],
                    'itemtype' => "mod",
                    'itemmodule' => "assign",
                    'grademax' => $a['maxgrade'],
                    'graderaw' => $score,
                    'percentageformatted' => $score !== null ? intval(($score / $a['maxgrade']) * 100) . '%' : '-'
                ];
            }
            foreach ($quizzes as $q) {
                $score = intval($q['sumgrades'] * 0.8);
                $gradeitems[] = [
                    'itemname' => $q['name'],
                    'itemtype' => "mod",
                    'itemmodule' => "quiz",
                    'grademax' => $q['sumgrades'],
                    'graderaw' => $score,
                    'percentageformatted' => intval(($score / $q['sumgrades']) * 100) . '%'
                ];
            }
            $gradeitems[] = [
                'itemname' => "Course Total",
                'itemtype' => "course",
                'grademax' => 100,
                'graderaw' => $s['grade'],
                'percentageformatted' => $s['grade'] . '%'
            ];

            $usergrades[] = [
                'userid' => $s['id'],
                'userfullname' => $s['name'],
                'gradeitems' => $gradeitems
            ];
        }

        // Add Student Demo itself
        $hg_gradeitems = [];
        foreach ($assigns as $a) {
            $isPast = $a['deadline'] < time();
            $score = $isPast ? intval($a['maxgrade'] * 0.88) : null;
            $hg_gradeitems[] = [
                'itemname' => $a['name'],
                'itemtype' => "mod",
                'itemmodule' => "assign",
                'grademax' => $a['maxgrade'],
                'graderaw' => $score,
                'percentageformatted' => $score !== null ? intval(($score / $a['maxgrade']) * 100) . '%' : '-'
            ];
        }
        foreach ($quizzes as $q) {
            $score = intval($q['sumgrades'] * 0.9);
            $hg_gradeitems[] = [
                'itemname' => $q['name'],
                'itemtype' => "mod",
                'itemmodule' => "quiz",
                'grademax' => $q['sumgrades'],
                'graderaw' => $score,
                'percentageformatted' => intval(($score / $q['sumgrades']) * 100) . '%'
            ];
        }
        $hg_gradeitems[] = [
            'itemname' => "Course Total",
            'itemtype' => "course",
            'grademax' => 100,
            'graderaw' => 86,
            'percentageformatted' => "86%"
        ];

        $usergrades[] = [
            'userid' => 1001,
            'userfullname' => "Gildong Hong",
            'gradeitems' => $hg_gradeitems
        ];

        return ['usergrades' => $usergrades];
    }

    public function updateStudentFeedback($courseid, $studentid, $feedbackText)
    {
        $courseid = intval($courseid);
        $studentid = intval($studentid);
        
        $studentsDb = session('students_db', []);
        if (isset($studentsDb[$courseid])) {
            foreach ($studentsDb[$courseid] as &$s) {
                if ($s['id'] === $studentid) {
                    $s['feedback'] = $feedbackText;
                    session(['students_db' => $studentsDb]);
                    return true;
                }
            }
        }
        return false;
    }
}
