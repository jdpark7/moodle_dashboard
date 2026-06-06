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
            'firstname' => "민수",
            'lastname' => "김",
            'fullname' => "김민수 교수",
            'userid' => 99,
            'userpictureurl' => "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200"
        ];

        $this->studentInfo = [
            'sitename' => "Antigravity University LMS",
            'username' => "student_hong",
            'firstname' => "길동",
            'lastname' => "홍",
            'fullname' => "홍길동",
            'userid' => 1001,
            'userpictureurl' => "https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200"
        ];

        $this->allCourses = [
            101 => [
                'id' => 101,
                'fullname' => "CSE201: 자료구조 (Data Structures)",
                'shortname' => "자료구조",
                'summary' => "리스트, 스택, 큐, 트리, 그래프 등의 기본 자료구조 개념과 알고리즘 분석 및 구현 방법을 학습합니다.",
                'startdate' => time() - 90 * 86400,
                'enrolledusercount' => 32,
                'progress' => 68
            ],
            102 => [
                'id' => 102,
                'fullname' => "CSE305: 인공지능 입문 (Introduction to AI)",
                'shortname' => "인공지능 입문",
                'summary' => "인공지능의 역사와 전통적인 탐색 방법, 기계학습, 신경망 및 딥러닝 입문 개념을 전반적으로 소개합니다.",
                'startdate' => time() - 90 * 86400,
                'enrolledusercount' => 45,
                'progress' => 52
            ],
            103 => [
                'id' => 103,
                'fullname' => "CSE402: 웹 시스템 설계 (Web Systems Design)",
                'shortname' => "웹 설계",
                'summary' => "풀스택 웹 프레임워크 설계와 REST API 구현, 데이터베이스 연동 및 배포 전략을 다룹니다.",
                'startdate' => time() - 90 * 86400,
                'enrolledusercount' => 24,
                'progress' => 74
            ],
            201 => [
                'id' => 201,
                'fullname' => "CSE302: 알고리즘 설계 및 분석 (Algorithm Design)",
                'shortname' => "알고리즘 분석",
                'summary' => "탐욕 알고리즘, 분할 정복, 동적 계획법 등 컴퓨터 알고리즘 설계 기법과 계산 복잡도를 체계적으로 다룹니다.",
                'startdate' => time() + 10 * 86400,
                'enrolledusercount' => 0,
                'progress' => 0
            ],
            202 => [
                'id' => 202,
                'fullname' => "CSE204: 데이터베이스 시스템 (Database Systems)",
                'shortname' => "데이터베이스",
                'summary' => "관계형 데이터베이스 모델, SQL 질의어 작성, 스키마 정규화 및 트랜잭션 개념과 인덱싱 기법을 학습합니다.",
                'startdate' => time() + 15 * 86400,
                'enrolledusercount' => 0,
                'progress' => 0
            ],
            203 => [
                'id' => 203,
                'fullname' => "CSE309: 컴퓨터 네트워크 (Computer Networks)",
                'shortname' => "컴퓨터 네트워크",
                'summary' => "TCP/IP 프로토콜 스택, 소켓 프로그래밍, 라우팅 및 스위칭 메커니즘과 네트워크 보안 기본을 다룹니다.",
                'startdate' => time() + 20 * 86400,
                'enrolledusercount' => 0,
                'progress' => 0
            ],
            // Past Courses
            99 => [
                'id' => 99,
                'fullname' => "CSE101: 컴퓨터 프로그래밍 입문 (Intro to Coding)",
                'shortname' => "프로그래밍 입문",
                'summary' => "Python을 활용하여 변수, 루프, 조건문, 함수 등 기초적인 프로그래밍 논리와 문제 해결을 습득합니다.",
                'startdate' => time() - 200 * 86400,
                'enrolledusercount' => 50,
                'progress' => 100
            ],
            98 => [
                'id' => 98,
                'fullname' => "MATH103: 이산수학 (Discrete Mathematics)",
                'shortname' => "이산수학",
                'summary' => "논리, 집합, 행렬, 관계, 그래프 이론 등 컴퓨터 과학 전반의 수학적 기초를 쌓습니다.",
                'startdate' => time() - 200 * 86400,
                'enrolledusercount' => 42,
                'progress' => 100
            ]
        ];

        $this->mockStudentTemplates = [
            ['id' => 2001, 'name' => "이지원", 'email' => "jw.lee@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2002, 'name' => "박민준", 'email' => "mj.park@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2003, 'name' => "김하은", 'email' => "he.kim@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2004, 'name' => "최우진", 'email' => "wj.choi@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2005, 'name' => "윤서연", 'email' => "sy.yoon@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2006, 'name' => "정도현", 'email' => "dh.jung@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2007, 'name' => "한민아", 'email' => "ma.han@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2008, 'name' => "강동우", 'email' => "dw.kang@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1552058544-f2b08422138a?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2009, 'name' => "조수아", 'email' => "sa.cho@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=150"],
            ['id' => 2010, 'name' => "송지호", 'email' => "jh.song@univ.ac.kr", 'pic' => "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=150"]
        ];

        $this->assignments = [
            101 => [
                ['id' => 1001, 'name' => "과제 1: LinkedList와 ArrayList 비교 분석 및 구현", 'deadline' => time() - 30 * 86400, 'maxgrade' => 100],
                ['id' => 1002, 'name' => "과제 2: 스택(Stack)을 이용한 계산기 프로그램 작성", 'deadline' => time() - 4 * 86400, 'maxgrade' => 100],
                ['id' => 1003, 'name' => "과제 3: 이진 검색 트리(BST) 연산 기능 최적화", 'deadline' => time() + 8 * 86400, 'maxgrade' => 100]
            ],
            102 => [
                ['id' => 1004, 'name' => "실습 1: 너비/깊이 우선 탐색 알고리즘 미로 찾기", 'deadline' => time() - 20 * 86400, 'maxgrade' => 100],
                ['id' => 1005, 'name' => "프로젝트 1: Scikit-learn을 이용한 주택 가격 예측", 'deadline' => time() - 2 * 86400, 'maxgrade' => 100],
                ['id' => 1006, 'name' => "프로젝트 2: PyTorch/Tensorflow 활용 CNN 이미지 분류기", 'deadline' => time() + 15 * 86400, 'maxgrade' => 100]
            ],
            103 => [
                ['id' => 1007, 'name' => "과제 1: HTML5/CSS3 활용 반응형 자기소개 페이지", 'deadline' => time() - 15 * 86400, 'maxgrade' => 50],
                ['id' => 1008, 'name' => "과제 2: ExpressJS 백엔드 구축 및 REST API 실습", 'deadline' => time() + 3 * 86400, 'maxgrade' => 100]
            ],
            201 => [
                ['id' => 1009, 'name' => "과제 1: 분할 정복 기반 Closest Pair 알고리즘 구현", 'deadline' => time() + 20 * 86400, 'maxgrade' => 100]
            ],
            202 => [
                ['id' => 1010, 'name' => "과제 1: 대학 정보 시스템 데이터베이스 스키마 E-R 모델 설계", 'deadline' => time() + 25 * 86400, 'maxgrade' => 100]
            ],
            203 => [
                ['id' => 1011, 'name' => "실습 1: Python Socket API 활용 멀티스레드 채팅 서버", 'deadline' => time() + 30 * 86400, 'maxgrade' => 100]
            ]
        ];

        $this->quizzes = [
            101 => [['id' => 801, 'name' => "자료구조 중간고사 퀴즈 (중요)", 'timeclose' => time() - 15 * 86400, 'sumgrades' => 20]],
            102 => [['id' => 802, 'name' => "기계학습 기초 개념 쪽지시험", 'timeclose' => time() - 25 * 86400, 'sumgrades' => 15]],
            103 => [['id' => 803, 'name' => "JavaScript DOM 조작 테스트", 'timeclose' => time() - 10 * 86400, 'sumgrades' => 30]],
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
                        'feedback' => $isRisk ? "7일 이상 접속 기록이 감지되지 않아 주의 깊은 모니터링이 필요합니다." : "정상 학습 수행 중인 학생입니다.",
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
                'itemname' => "과정 총합계",
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
            'itemname' => "과정 총합계",
            'itemtype' => "course",
            'grademax' => 100,
            'graderaw' => 86,
            'percentageformatted' => "86%"
        ];

        $usergrades[] = [
            'userid' => 1001,
            'userfullname' => "홍길동",
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
