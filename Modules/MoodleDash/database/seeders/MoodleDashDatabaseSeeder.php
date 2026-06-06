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
            'lastname' => '김',
            'firstname' => '민수',
            'email' => 'prof_kim@univ.ac.kr',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'userpictureurl' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
            'lastaccess' => time()
        ]);

        // 2. Create Student (Demo)
        $studentDemo = User::create([
            'username' => 'student_hong',
            'lastname' => '홍',
            'firstname' => '길동',
            'email' => 'student_hong@univ.ac.kr',
            'password' => Hash::make('password'),
            'role' => 'student',
            'userpictureurl' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200',
            'lastaccess' => time()
        ]);

        // 3. Create Mock Students
        $mockStudentsInfo = [
            ['username' => 'student_lee', 'last' => '이', 'first' => '지원', 'email' => 'jw.lee@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_park', 'last' => '박', 'first' => '민준', 'email' => 'mj.park@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_hekim', 'last' => '김', 'first' => '하은', 'email' => 'he.kim@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_choi', 'last' => '최', 'first' => '우진', 'email' => 'wj.choi@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_yoon', 'last' => '윤', 'first' => '서연', 'email' => 'sy.yoon@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_jung', 'last' => '정', 'first' => '도현', 'email' => 'dh.jung@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_han', 'last' => '한', 'first' => '민아', 'email' => 'ma.han@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_kang', 'last' => '강', 'first' => '동우', 'email' => 'dw.kang@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_cho', 'last' => '조', 'first' => '수아', 'email' => 'sa.cho@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=150'],
            ['username' => 'student_song', 'last' => '송', 'first' => '지호', 'email' => 'jh.song@univ.ac.kr', 'pic' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=150']
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
                'fullname' => "CSE201: 자료구조 (Data Structures)",
                'shortname' => "자료구조",
                'summary' => "리스트, 스택, 큐, 트리, 그래프 등의 기본 자료구조 개념과 알고리즘 분석 및 구현 방법을 학습합니다.",
                'topics' => json_encode(['리스트 기초', '스택 & 큐 구현', '이진 트리 연산', '그래프 최단경로'])
            ],
            102 => [
                'fullname' => "CSE305: 인공지능 입문 (Introduction to AI)",
                'shortname' => "인공지능 입문",
                'summary' => "인공지능의 역사와 전통적인 탐색 방법, 기계학습, 신경망 및 딥러닝 입문 개념을 전반적으로 소개합니다.",
                'topics' => json_encode(['탐색 알고리즘', '기계 학습 기초', '지도 학습 모델', '신경망 입문'])
            ],
            103 => [
                'fullname' => "CSE402: 웹 시스템 설계 (Web Systems Design)",
                'shortname' => "웹 설계",
                'summary' => "풀스택 웹 프레임워크 설계와 REST API 구현, 데이터베이스 연동 및 배포 전략을 다룹니다.",
                'topics' => json_encode(['HTML/CSS 레이아웃', 'ExpressJS 백엔드', '데이터베이스 모델링', '클라우드 배포'])
            ],
            // Additional Catalog Courses
            201 => [
                'fullname' => "CSE302: 알고리즘 설계 및 분석 (Algorithm Design)",
                'shortname' => "알고리즘 분석",
                'summary' => "탐욕 알고리즘, 분할 정복, 동적 계획법 등 컴퓨터 알고리즘 설계 기법과 계산 복잡도를 체계적으로 다룹니다.",
                'topics' => json_encode(['분할 정복 기법', '동적 계획법 실습', 'NP-완비 문제 개념'])
            ],
            202 => [
                'fullname' => "CSE204: 데이터베이스 시스템 (Database Systems)",
                'shortname' => "데이터베이스",
                'summary' => "관계형 데이터베이스 모델, SQL 질의어 작성, 스키마 정규화 및 트랜잭션 개념과 인덱싱 기법을 학습합니다.",
                'topics' => json_encode(['관계 대수', 'SQL 작성법', '정규화 이론'])
            ],
            203 => [
                'fullname' => "CSE309: 컴퓨터 네트워크 (Computer Networks)",
                'shortname' => "컴퓨터 네트워크",
                'summary' => "TCP/IP 프로토콜 스택, 소켓 프로그래밍, 라우팅 및 스위칭 메커니즘과 네트워크 보안 기본을 다룹니다.",
                'topics' => json_encode(['TCP/IP 계층 구조', '소켓 통신 설계', '네트워크 라우팅'])
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
        Enrollment::create(['user_id' => $studentDemo->id, 'course_id' => 101, 'progress' => 68, 'feedback' => '우수한 학습 참여도를 보이고 있습니다.']);
        Enrollment::create(['user_id' => $studentDemo->id, 'course_id' => 102, 'progress' => 52, 'feedback' => '과제 제출 완성도가 높습니다.']);
        Enrollment::create(['user_id' => $studentDemo->id, 'course_id' => 103, 'progress' => 74, 'feedback' => '웹 기술 활용 능력이 뛰어납니다.']);

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
                        ? '7일 이상 접속 기록이 감지되지 않아 주의 깊은 모니터링이 필요합니다.' 
                        : '정상 학습 수행 중인 학생입니다.'
                ]);
            }
        }

        // 6. Create Assignments
        $assignmentsInfo = [
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
