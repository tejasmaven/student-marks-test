<?php

class StudentsController
{
    public function handle(string $page): void
    {
        $db = db();
        $action = $_GET['action'] ?? 'list';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validate_csrf();
        }

        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            if ($name === '' || $gradeId < 1 || $gradeId > 10) {
                set_flash('danger', 'Name and grade are required.');
                redirect('index.php?page=students&action=create');
            }
            execute_stmt($db, 'INSERT INTO students (name, grade_id) VALUES (?, ?)', 'si', [$name, $gradeId]);
            set_flash('success', 'Student created successfully.');
            redirect('index.php?page=students');
        }

        if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = (int)($_POST['student_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            if ($studentId <= 0 || $name === '' || $gradeId < 1 || $gradeId > 10) {
                set_flash('danger', 'Invalid student data.');
                redirect('index.php?page=students');
            }
            execute_stmt($db, 'UPDATE students SET name = ?, grade_id = ? WHERE student_id = ?', 'sii', [$name, $gradeId, $studentId]);
            set_flash('success', 'Student updated successfully.');
            redirect('index.php?page=students');
        }

        if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = (int)($_POST['student_id'] ?? 0);
            if ($studentId <= 0) {
                set_flash('danger', 'Invalid student selection.');
                redirect('index.php?page=students');
            }
            execute_stmt($db, 'DELETE FROM students WHERE student_id = ?', 'i', [$studentId]);
            set_flash('success', 'Student deleted.');
            redirect('index.php?page=students');
        }

        $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');

        if ($action === 'create') {
            $content_view = __DIR__ . '/../views/students/form.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($action === 'edit') {
            $studentId = (int)($_GET['id'] ?? 0);
            $student = $studentId ? query_one($db, 'SELECT * FROM students WHERE student_id = ?', 'i', [$studentId]) : null;
            if (!$student) {
                set_flash('danger', 'Student not found.');
                redirect('index.php?page=students');
            }
            $content_view = __DIR__ . '/../views/students/form.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        $gradeFilter = (int)($_GET['grade_id'] ?? 0);
        $search = trim($_GET['search'] ?? '');
        $pageNum = (int)($_GET['p'] ?? 1);
        $perPage = 10;
        $where = [];
        $params = [];
        $types = '';
        if ($gradeFilter) {
            $where[] = 's.grade_id = ?';
            $params[] = $gradeFilter;
            $types .= 'i';
        }
        if ($search !== '') {
            $where[] = 's.name LIKE ?';
            $params[] = '%' . $search . '%';
            $types .= 's';
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $countRow = query_one($db, "SELECT COUNT(*) AS cnt FROM students s $whereSql", $types, $params);
        $pagination = paginate((int)($countRow['cnt'] ?? 0), $perPage, $pageNum);

        $params[] = $pagination['per_page'];
        $params[] = $pagination['offset'];
        $types .= 'ii';

        $students = query_all(
            $db,
            "SELECT s.*, g.grade_id FROM students s JOIN grades g ON s.grade_id = g.grade_id $whereSql ORDER BY s.name LIMIT ? OFFSET ?",
            $types,
            $params
        );

        $content_view = __DIR__ . '/../views/students/list.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
