<?php

class DashboardController
{
    public function handle(string $page): void
    {
        $db = db();
        $academicYear = current_academic_year();

        $stats = [];
        $stats['students'] = (int)($db->query('SELECT COUNT(*) AS cnt FROM students')->fetch_assoc()['cnt'] ?? 0);
        $stats['subjects'] = (int)($db->query('SELECT COUNT(*) AS cnt FROM subjects')->fetch_assoc()['cnt'] ?? 0);
        $stats['marks'] = (int)($db->query('SELECT COUNT(*) AS cnt FROM marks')->fetch_assoc()['cnt'] ?? 0);

        $passFail = query_one(
            $db,
            "SELECT SUM(CASE WHEN m.marks >= ? THEN 1 ELSE 0 END) AS pass_count,
                    SUM(CASE WHEN m.marks < ? THEN 1 ELSE 0 END) AS fail_count
             FROM marks m
             WHERE m.academic_year = ?",
            'iii',
            [PASSING_MARK, PASSING_MARK, $academicYear]
        );

        $expectedRow = query_one(
            $db,
            "SELECT SUM(student_count * subject_count * 4) AS expected
             FROM (
                SELECT g.grade_id,
                       (SELECT COUNT(*) FROM students s WHERE s.grade_id = g.grade_id) AS student_count,
                       (SELECT COUNT(*) FROM grade_subjects gs WHERE gs.grade_id = g.grade_id) AS subject_count
                FROM grades g
             ) AS t"
        );
        $expected = (int)($expectedRow['expected'] ?? 0);

        $actualRow = query_one(
            $db,
            "SELECT COUNT(*) AS actual
             FROM marks m
             WHERE m.academic_year = ?",
            'i',
            [$academicYear]
        );
        $missing = max(0, $expected - (int)($actualRow['actual'] ?? 0));

        $gradePassFail = query_all(
            $db,
            "SELECT g.grade_id,
                    SUM(CASE WHEN m.marks >= ? THEN 1 ELSE 0 END) AS pass_count,
                    SUM(CASE WHEN m.marks < ? THEN 1 ELSE 0 END) AS fail_count
             FROM grades g
             LEFT JOIN students s ON s.grade_id = g.grade_id
             LEFT JOIN marks m ON m.student_id = s.student_id AND m.academic_year = ?
             GROUP BY g.grade_id
             ORDER BY g.grade_id",
            'iii',
            [PASSING_MARK, PASSING_MARK, $academicYear]
        );

        $content_view = __DIR__ . '/../views/dashboard/index.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
