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
                    'SELECT m.subject_id, e.exam_no, m.marks FROM marks m JOIN exams e ON m.exam_id = e.exam_id WHERE m.student_id = ? AND e.academic_year = ?',
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
                    'SELECT s.subject_id, e.exam_no,
                            AVG(m.marks) AS avg_mark,
                            SUM(CASE WHEN m.marks >= ? THEN 1 ELSE 0 END) AS pass_count,
                            SUM(CASE WHEN m.marks < ? THEN 1 ELSE 0 END) AS fail_count
                     FROM students st
                     JOIN marks m ON st.student_id = m.student_id
                     JOIN exams e ON m.exam_id = e.exam_id
                     JOIN subjects s ON m.subject_id = s.subject_id
                     WHERE st.grade_id = ? AND e.academic_year = ?
                     GROUP BY s.subject_id, e.exam_no',
                    'iiii',
                    [PASSING_MARK, PASSING_MARK, $gradeId, $academicYear]
                );

                $topStudents = query_all(
                    $db,
                    'SELECT st.student_id, st.name, AVG(m.marks) AS avg_mark
                     FROM students st
                     JOIN marks m ON st.student_id = m.student_id
                     JOIN exams e ON m.exam_id = e.exam_id
                     WHERE st.grade_id = ? AND e.academic_year = ?
                     GROUP BY st.student_id, st.name
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
                    'SELECT e.exam_no, m.marks FROM marks m
                     JOIN exams e ON m.exam_id = e.exam_id
                     JOIN students st ON m.student_id = st.student_id
                     WHERE st.grade_id = ? AND m.subject_id = ? AND e.academic_year = ?
                     ORDER BY e.exam_no',
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
                'SELECT st.student_id, st.name, st.grade_id, s.subject_id, s.name AS subject_name, e.exam_no
                 FROM students st
                 JOIN grade_subjects gs ON st.grade_id = gs.grade_id
                 JOIN subjects s ON gs.subject_id = s.subject_id
                 JOIN exams e ON e.academic_year = ?
                 LEFT JOIN marks m ON m.student_id = st.student_id AND m.subject_id = s.subject_id AND m.exam_id = e.exam_id
                 WHERE m.mark_id IS NULL' . ($gradeId ? ' AND st.grade_id = ?' : '') . '
                 ORDER BY st.grade_id, st.name, s.name, e.exam_no',
                $gradeId ? 'ii' : 'i',
                $gradeId ? [$academicYear, $gradeId] : [$academicYear]
            );

            $expectedRow = query_one(
                $db,
                'SELECT SUM(student_count * subject_count * 4) AS expected
                 FROM (
                    SELECT g.grade_id,
                           (SELECT COUNT(*) FROM students s WHERE s.grade_id = g.grade_id) AS student_count,
                           (SELECT COUNT(*) FROM grade_subjects gs WHERE gs.grade_id = g.grade_id) AS subject_count
                    FROM grades g
                 ) AS t'
            );
            $expected = (int)($expectedRow['expected'] ?? 0);

            $actualRow = query_one(
                $db,
                'SELECT COUNT(*) AS actual
                 FROM marks m
                 JOIN exams e ON m.exam_id = e.exam_id
                 WHERE e.academic_year = ?' . ($gradeId ? ' AND m.student_id IN (SELECT student_id FROM students WHERE grade_id = ?)' : ''),
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
