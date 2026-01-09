<?php

class ExamsController
{
    public function handle(string $page): void
    {
        $db = db();
        $action = $_GET['action'] ?? 'list';
        $academicYear = (int)($_GET['academic_year'] ?? DEFAULT_ACADEMIC_YEAR);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validate_csrf();
        }

        if ($action === 'generate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            for ($i = 1; $i <= 4; $i++) {
                $exists = query_one($db, 'SELECT exam_id FROM exams WHERE academic_year = ? AND exam_no = ?', 'ii', [$academicYear, $i]);
                if (!$exists) {
                    execute_stmt($db, 'INSERT INTO exams (academic_year, exam_no, exam_name) VALUES (?, ?, ?)', 'iis', [$academicYear, $i, 'Exam ' . $i]);
                }
            }
            set_flash('success', 'Exam slots generated for the academic year.');
            redirect('index.php?page=exams&academic_year=' . $academicYear);
        }

        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $examNo = (int)($_POST['exam_no'] ?? 0);
            $examName = trim($_POST['exam_name'] ?? '');
            if ($examNo < 1 || $examNo > 4 || $examName === '') {
                set_flash('danger', 'Exam number must be 1 to 4 and name is required.');
                redirect('index.php?page=exams&action=create&academic_year=' . $academicYear);
            }
            $exists = query_one($db, 'SELECT exam_id FROM exams WHERE academic_year = ? AND exam_no = ?', 'ii', [$academicYear, $examNo]);
            if ($exists) {
                set_flash('danger', 'Exam number already exists for this year.');
                redirect('index.php?page=exams&action=create&academic_year=' . $academicYear);
            }
            execute_stmt($db, 'INSERT INTO exams (academic_year, exam_no, exam_name) VALUES (?, ?, ?)', 'iis', [$academicYear, $examNo, $examName]);
            set_flash('success', 'Exam created successfully.');
            redirect('index.php?page=exams&academic_year=' . $academicYear);
        }

        if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            $examNo = (int)($_POST['exam_no'] ?? 0);
            $examName = trim($_POST['exam_name'] ?? '');
            if ($examId <= 0 || $examNo < 1 || $examNo > 4 || $examName === '') {
                set_flash('danger', 'Invalid exam data.');
                redirect('index.php?page=exams&academic_year=' . $academicYear);
            }
            $exists = query_one($db, 'SELECT exam_id FROM exams WHERE academic_year = ? AND exam_no = ? AND exam_id != ?', 'iii', [$academicYear, $examNo, $examId]);
            if ($exists) {
                set_flash('danger', 'Exam number already exists for this year.');
                redirect('index.php?page=exams&action=edit&id=' . $examId . '&academic_year=' . $academicYear);
            }
            execute_stmt($db, 'UPDATE exams SET exam_no = ?, exam_name = ? WHERE exam_id = ?', 'isi', [$examNo, $examName, $examId]);
            set_flash('success', 'Exam updated successfully.');
            redirect('index.php?page=exams&academic_year=' . $academicYear);
        }

        if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            $hasMarks = query_one($db, 'SELECT 1 FROM marks WHERE exam_id = ? LIMIT 1', 'i', [$examId]);
            if ($hasMarks) {
                set_flash('danger', 'Cannot delete exam because marks exist.');
                redirect('index.php?page=exams&academic_year=' . $academicYear);
            }
            execute_stmt($db, 'DELETE FROM exams WHERE exam_id = ?', 'i', [$examId]);
            set_flash('success', 'Exam deleted successfully.');
            redirect('index.php?page=exams&academic_year=' . $academicYear);
        }

        if ($action === 'create') {
            $content_view = __DIR__ . '/../views/exams/form.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($action === 'edit') {
            $examId = (int)($_GET['id'] ?? 0);
            $exam = $examId ? query_one($db, 'SELECT * FROM exams WHERE exam_id = ?', 'i', [$examId]) : null;
            if (!$exam) {
                set_flash('danger', 'Exam not found.');
                redirect('index.php?page=exams&academic_year=' . $academicYear);
            }
            $content_view = __DIR__ . '/../views/exams/form.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        $exams = query_all(
            $db,
            'SELECT * FROM exams WHERE academic_year = ? ORDER BY exam_no',
            'i',
            [$academicYear]
        );

        $content_view = __DIR__ . '/../views/exams/list.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
