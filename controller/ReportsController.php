<?php

class ReportsController
{
    public function handle(string $page): void
    {
        $db = db();
        $academicYear = (int)($_GET['academic_year'] ?? DEFAULT_ACADEMIC_YEAR);

        if ($page === 'reports_student') {
            $students = query_all($db, 'SELECT student_id, name, grade_id FROM students ORDER BY name');
            $studentId = (int)($_GET['student_id'] ?? 0);
            $student = $studentId ? query_one($db, 'SELECT * FROM students WHERE student_id = ?', 'i', [$studentId]) : null;
            $subjects = [];
            $exams = query_all($db, 'SELECT * FROM exams WHERE academic_year = ? ORDER BY exam_no', 'i', [$academicYear]);
            $marksMap = [];

            if ($student) {
                $subjects = query_all(
                    $db,
                    'SELECT s.subject_id, s.name FROM grade_subjects gs JOIN subjects s ON gs.subject_id = s.subject_id WHERE gs.grade_id = ? ORDER BY s.name',
                    'i',
                    [$student['grade_id']]
                );
                $marksRows = query_all(
                    $db,
                    'SELECT subject_id, exam_no, marks FROM v_student_marks WHERE student_id = ? AND academic_year = ?',
                    'ii',
                    [$studentId, $academicYear]
                );
                foreach ($marksRows as $row) {
                    $marksMap[$row['subject_id']][$row['exam_no']] = $row['marks'];
                }
            }

            $content_view = __DIR__ . '/../views/reports/student_report_card.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($page === 'reports_grade_summary') {
            $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');
            $gradeId = (int)($_GET['grade_id'] ?? 0);
            $subjects = [];
            $summaryRows = [];
            $topStudents = [];

            if ($gradeId) {
                $subjects = query_all(
                    $db,
                    'SELECT s.subject_id, s.name FROM grade_subjects gs JOIN subjects s ON gs.subject_id = s.subject_id WHERE gs.grade_id = ? ORDER BY s.name',
                    'i',
                    [$gradeId]
                );
                $summaryRows = query_all(
                    $db,
                    'SELECT subject_id, exam_no, avg_mark, pass_count, fail_count
                     FROM v_grade_summary
                     WHERE grade_id = ? AND academic_year = ?
                     ORDER BY subject_id, exam_no',
                    'ii',
                    [$gradeId, $academicYear]
                );

                $topStudents = query_all(
                    $db,
                    'SELECT student_id, name, avg_mark
                     FROM v_grade_student_averages
                     WHERE grade_id = ? AND academic_year = ?
                     ORDER BY avg_mark DESC
                     LIMIT 5',
                    'ii',
                    [$gradeId, $academicYear]
                );
            }

            $content_view = __DIR__ . '/../views/reports/grade_summary.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($page === 'reports_subject_performance') {
            $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');
            $gradeId = (int)($_GET['grade_id'] ?? 0);
            $subjectId = (int)($_GET['subject_id'] ?? 0);
            $subjects = [];
            if ($gradeId) {
                $subjects = query_all(
                    $db,
                    'SELECT s.subject_id, s.name FROM grade_subjects gs JOIN subjects s ON gs.subject_id = s.subject_id WHERE gs.grade_id = ? ORDER BY s.name',
                    'i',
                    [$gradeId]
                );
            }

            $distribution = [];
            $trend = [];
            if ($gradeId && $subjectId) {
                $rows = query_all(
                    $db,
                    'SELECT exam_no, marks
                     FROM v_subject_performance_marks
                     WHERE grade_id = ? AND subject_id = ? AND academic_year = ?
                     ORDER BY exam_no',
                    'iii',
                    [$gradeId, $subjectId, $academicYear]
                );
                foreach ($rows as $row) {
                    $examNo = $row['exam_no'];
                    $mark = (float)$row['marks'];
                    if (!isset($distribution[$examNo])) {
                        $distribution[$examNo] = [
                            '<35' => 0,
                            '35-44' => 0,
                            '45-60' => 0,
                            '61-80' => 0,
                            '81-100' => 0,
                            'total' => 0,
                            'sum' => 0,
                        ];
                    }
                    if ($mark < 35) {
                        $distribution[$examNo]['<35']++;
                    } elseif ($mark <= 44) {
                        $distribution[$examNo]['35-44']++;
                    } elseif ($mark <= 60) {
                        $distribution[$examNo]['45-60']++;
                    } elseif ($mark <= 80) {
                        $distribution[$examNo]['61-80']++;
                    } else {
                        $distribution[$examNo]['81-100']++;
                    }
                    $distribution[$examNo]['total']++;
                    $distribution[$examNo]['sum'] += $mark;
                }
                foreach ($distribution as $examNo => $data) {
                    $trend[$examNo] = $data['total'] ? ($data['sum'] / $data['total']) : 0;
                }
            }

            $content_view = __DIR__ . '/../views/reports/subject_performance.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($page === 'reports_missing_marks') {
            $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');
            $gradeId = (int)($_GET['grade_id'] ?? 0);

            $missingRows = query_all(
                $db,
                'SELECT student_id, student_name AS name, grade_id, subject_id, subject_name, exam_no
                 FROM v_missing_marks
                 WHERE academic_year = ?' . ($gradeId ? ' AND grade_id = ?' : '') . '
                 ORDER BY grade_id, student_name, subject_name, exam_no',
                $gradeId ? 'ii' : 'i',
                $gradeId ? [$academicYear, $gradeId] : [$academicYear]
            );

            $expectedRow = query_one(
                $db,
                'SELECT ' . ($gradeId ? 'expected_count AS expected' : 'SUM(expected_count) AS expected') . '
                 FROM v_expected_marks
                 WHERE academic_year = ?' . ($gradeId ? ' AND grade_id = ?' : ''),
                $gradeId ? 'ii' : 'i',
                $gradeId ? [$academicYear, $gradeId] : [$academicYear]
            );
            $expected = (int)($expectedRow['expected'] ?? 0);

            $actualRow = query_one(
                $db,
                'SELECT ' . ($gradeId ? 'actual_count AS actual' : 'SUM(actual_count) AS actual') . '
                 FROM v_actual_marks
                 WHERE academic_year = ?' . ($gradeId ? ' AND grade_id = ?' : ''),
                $gradeId ? 'ii' : 'i',
                $gradeId ? [$academicYear, $gradeId] : [$academicYear]
            );
            $missingTotal = max(0, $expected - (int)($actualRow['actual'] ?? 0));

            $content_view = __DIR__ . '/../views/reports/missing_marks.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }
    }
}
