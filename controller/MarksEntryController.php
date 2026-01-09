<?php

class MarksEntryController
{
    public function handle(string $page): void
    {
        $db = db();
        $step = $_GET['step'] ?? '1';
        $academicYear = (int)($_GET['academic_year'] ?? DEFAULT_ACADEMIC_YEAR);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validate_csrf();
        }

        if ($step === '2' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $examId = (int)($_POST['exam_id'] ?? 0);
            if ($gradeId <= 0 || $subjectId <= 0 || $examId <= 0) {
                set_flash('danger', 'Please select grade, subject, and exam.');
                redirect('index.php?page=marks_entry');
            }
            redirect('index.php?page=marks_entry&step=2&grade_id=' . $gradeId . '&subject_id=' . $subjectId . '&exam_id=' . $examId . '&academic_year=' . $academicYear);
        }

        if ($step === 'download_template') {
            $gradeId = (int)($_GET['grade_id'] ?? 0);
            $subjectId = (int)($_GET['subject_id'] ?? 0);
            $examId = (int)($_GET['exam_id'] ?? 0);
            if ($gradeId <= 0 || $subjectId <= 0 || $examId <= 0) {
                set_flash('danger', 'Invalid marks context.');
                redirect('index.php?page=marks_entry');
            }

            $students = query_all($db, 'SELECT student_id, name FROM students WHERE grade_id = ? ORDER BY name', 'i', [$gradeId]);
            $filename = 'marks_template_grade_' . $gradeId . '_subject_' . $subjectId . '_exam_' . $examId . '_' . $academicYear . '.csv';

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['student_id', 'student_name', 'marks']);
            foreach ($students as $student) {
                fputcsv($output, [$student['student_id'], $student['name'], '']);
            }
            fclose($output);
            return;
        }

        if ($step === 'import' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $examId = (int)($_POST['exam_id'] ?? 0);
            if ($gradeId <= 0 || $subjectId <= 0 || $examId <= 0) {
                set_flash('danger', 'Invalid marks context.');
                redirect('index.php?page=marks_entry');
            }

            if (empty($_FILES['marks_file']['tmp_name']) || !is_uploaded_file($_FILES['marks_file']['tmp_name'])) {
                set_flash('danger', 'Please upload a CSV file.');
                redirect('index.php?page=marks_entry&step=2&grade_id=' . $gradeId . '&subject_id=' . $subjectId . '&exam_id=' . $examId . '&academic_year=' . $academicYear);
            }

            $students = query_all($db, 'SELECT student_id FROM students WHERE grade_id = ?', 'i', [$gradeId]);
            $allowedStudents = [];
            foreach ($students as $student) {
                $allowedStudents[(int)$student['student_id']] = true;
            }

            $handle = fopen($_FILES['marks_file']['tmp_name'], 'r');
            $header = fgetcsv($handle);
            $columnIndex = ['student_id' => 0, 'marks' => 2];
            if ($header) {
                $lowerHeader = array_map('strtolower', $header);
                if (in_array('student_id', $lowerHeader, true) && in_array('marks', $lowerHeader, true)) {
                    $columnIndex['student_id'] = array_search('student_id', $lowerHeader, true);
                    $columnIndex['marks'] = array_search('marks', $lowerHeader, true);
                } else {
                    rewind($handle);
                }
            }

            $stmt = $db->prepare('INSERT INTO marks (student_id, subject_id, exam_id, marks) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks = VALUES(marks)');
            $imported = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $studentId = (int)($row[$columnIndex['student_id']] ?? 0);
                $markValue = $row[$columnIndex['marks']] ?? null;
                $markValue = is_numeric($markValue) ? (float)$markValue : null;
                if ($studentId <= 0 || $markValue === null || $markValue < 0 || $markValue > TOTAL_MARKS) {
                    continue;
                }
                if (!isset($allowedStudents[$studentId])) {
                    continue;
                }
                $stmt->bind_param('iiid', $studentId, $subjectId, $examId, $markValue);
                $stmt->execute();
                $imported++;
            }
            fclose($handle);
            $stmt->close();

            set_flash('success', 'Imported ' . $imported . ' marks from CSV.');
            redirect('index.php?page=marks_entry&step=2&grade_id=' . $gradeId . '&subject_id=' . $subjectId . '&exam_id=' . $examId . '&academic_year=' . $academicYear);
        }

        if ($step === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $examId = (int)($_POST['exam_id'] ?? 0);
            $marksData = $_POST['marks'] ?? [];

            if ($gradeId <= 0 || $subjectId <= 0 || $examId <= 0) {
                set_flash('danger', 'Invalid marks context.');
                redirect('index.php?page=marks_entry');
            }

            $stmt = $db->prepare('INSERT INTO marks (student_id, subject_id, exam_id, marks) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks = VALUES(marks)');
            foreach ($marksData as $studentId => $markValue) {
                $studentId = (int)$studentId;
                $markValue = is_numeric($markValue) ? (float)$markValue : null;
                if ($studentId <= 0 || $markValue === null || $markValue < 0 || $markValue > TOTAL_MARKS) {
                    continue;
                }
                $stmt->bind_param('iiid', $studentId, $subjectId, $examId, $markValue);
                $stmt->execute();
            }
            $stmt->close();

            set_flash('success', 'Marks saved successfully.');
            redirect('index.php?page=marks_entry&step=3&grade_id=' . $gradeId . '&subject_id=' . $subjectId . '&exam_id=' . $examId . '&academic_year=' . $academicYear);
        }

        $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');
        $exams = query_all($db, 'SELECT * FROM exams WHERE academic_year = ? ORDER BY exam_no', 'i', [$academicYear]);

        if ($step === '2') {
            $gradeId = (int)($_GET['grade_id'] ?? 0);
            $subjectId = (int)($_GET['subject_id'] ?? 0);
            $examId = (int)($_GET['exam_id'] ?? 0);

            $students = query_all($db, 'SELECT student_id, name FROM students WHERE grade_id = ? ORDER BY name', 'i', [$gradeId]);
            $subject = query_one($db, 'SELECT * FROM subjects WHERE subject_id = ?', 'i', [$subjectId]);
            $exam = query_one($db, 'SELECT * FROM exams WHERE exam_id = ?', 'i', [$examId]);

            $marks = query_all(
                $db,
                'SELECT student_id, marks FROM marks WHERE subject_id = ? AND exam_id = ?',
                'ii',
                [$subjectId, $examId]
            );
            $marksMap = [];
            foreach ($marks as $markRow) {
                $marksMap[$markRow['student_id']] = $markRow['marks'];
            }

            $content_view = __DIR__ . '/../views/marks_entry/step2_grid.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($step === '3') {
            $gradeId = (int)($_GET['grade_id'] ?? 0);
            $subjectId = (int)($_GET['subject_id'] ?? 0);
            $examId = (int)($_GET['exam_id'] ?? 0);

            $summary = query_one(
                $db,
                'SELECT SUM(CASE WHEN marks >= ? THEN 1 ELSE 0 END) AS pass_count,
                        SUM(CASE WHEN marks < ? THEN 1 ELSE 0 END) AS fail_count,
                        AVG(marks) AS avg_mark
                 FROM marks
                 WHERE subject_id = ? AND exam_id = ?',
                'iiii',
                [PASSING_MARK, PASSING_MARK, $subjectId, $examId]
            );

            $studentCount = query_one($db, 'SELECT COUNT(*) AS cnt FROM students WHERE grade_id = ?', 'i', [$gradeId]);
            $enteredCount = query_one(
                $db,
                'SELECT COUNT(*) AS cnt FROM marks m JOIN students s ON m.student_id = s.student_id WHERE s.grade_id = ? AND m.subject_id = ? AND m.exam_id = ?',
                'iii',
                [$gradeId, $subjectId, $examId]
            );
            $missing = max(0, (int)($studentCount['cnt'] ?? 0) - (int)($enteredCount['cnt'] ?? 0));

            $content_view = __DIR__ . '/../views/marks_entry/step3_summary.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        $gradeId = (int)($_GET['grade_id'] ?? 0);
        $subjects = [];
        if ($gradeId) {
            $subjects = query_all(
                $db,
                'SELECT s.subject_id, s.code, s.name FROM grade_subjects gs JOIN subjects s ON gs.subject_id = s.subject_id WHERE gs.grade_id = ? ORDER BY s.name',
                'i',
                [$gradeId]
            );
        }

        $content_view = __DIR__ . '/../views/marks_entry/step1_context.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
