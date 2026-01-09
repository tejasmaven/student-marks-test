<?php

class SubjectsController
{
    public function handle(string $page): void
    {
        $db = db();
        $action = $_GET['action'] ?? 'list';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validate_csrf();
        }

        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $name = trim($_POST['name'] ?? '');
            if ($code === '' || $name === '') {
                set_flash('danger', 'Code and name are required.');
                redirect('index.php?page=subjects&action=create');
            }

            $exists = query_one($db, 'SELECT subject_id FROM subjects WHERE code = ? OR name = ?', 'ss', [$code, $name]);
            if ($exists) {
                set_flash('danger', 'Subject code or name already exists.');
                redirect('index.php?page=subjects&action=create');
            }

            execute_stmt($db, 'INSERT INTO subjects (code, name) VALUES (?, ?)', 'ss', [$code, $name]);
            set_flash('success', 'Subject created successfully.');
            redirect('index.php?page=subjects');
        }

        if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $code = trim($_POST['code'] ?? '');
            $name = trim($_POST['name'] ?? '');
            if ($subjectId <= 0 || $code === '' || $name === '') {
                set_flash('danger', 'Invalid subject data.');
                redirect('index.php?page=subjects');
            }

            $exists = query_one(
                $db,
                'SELECT subject_id FROM subjects WHERE (code = ? OR name = ?) AND subject_id != ?',
                'ssi',
                [$code, $name, $subjectId]
            );
            if ($exists) {
                set_flash('danger', 'Subject code or name already exists.');
                redirect('index.php?page=subjects&action=edit&id=' . $subjectId);
            }

            execute_stmt($db, 'UPDATE subjects SET code = ?, name = ? WHERE subject_id = ?', 'ssi', [$code, $name, $subjectId]);
            set_flash('success', 'Subject updated successfully.');
            redirect('index.php?page=subjects');
        }

        if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            if ($subjectId <= 0) {
                set_flash('danger', 'Invalid subject selection.');
                redirect('index.php?page=subjects');
            }

            $mapped = query_one($db, 'SELECT 1 FROM grade_subjects WHERE subject_id = ? LIMIT 1', 'i', [$subjectId]);
            $hasMarks = query_one($db, 'SELECT 1 FROM marks WHERE subject_id = ? LIMIT 1', 'i', [$subjectId]);
            if ($mapped || $hasMarks) {
                set_flash('danger', 'Cannot delete subject because it is mapped to grades or has marks.');
                redirect('index.php?page=subjects');
            }

            execute_stmt($db, 'DELETE FROM subjects WHERE subject_id = ?', 'i', [$subjectId]);
            set_flash('success', 'Subject deleted successfully.');
            redirect('index.php?page=subjects');
        }

        if ($action === 'create') {
            $content_view = __DIR__ . '/../views/subjects/form.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        if ($action === 'edit') {
            $subjectId = (int)($_GET['id'] ?? 0);
            $subject = $subjectId ? query_one($db, 'SELECT * FROM subjects WHERE subject_id = ?', 'i', [$subjectId]) : null;
            if (!$subject) {
                set_flash('danger', 'Subject not found.');
                redirect('index.php?page=subjects');
            }
            $content_view = __DIR__ . '/../views/subjects/form.php';
            require __DIR__ . '/../layout/layout.php';
            return;
        }

        $search = trim($_GET['search'] ?? '');
        $pageNum = (int)($_GET['p'] ?? 1);
        $perPage = 10;
        $where = '';
        $params = [];
        $types = '';
        if ($search !== '') {
            $where = 'WHERE code LIKE ? OR name LIKE ?';
            $like = '%' . $search . '%';
            $params = [$like, $like];
            $types = 'ss';
        }

        $countRow = query_one($db, "SELECT COUNT(*) AS cnt FROM subjects $where", $types, $params);
        $pagination = paginate((int)($countRow['cnt'] ?? 0), $perPage, $pageNum);

        $params[] = $pagination['per_page'];
        $params[] = $pagination['offset'];
        $types .= 'ii';
        $subjects = query_all(
            $db,
            "SELECT * FROM subjects $where ORDER BY name LIMIT ? OFFSET ?",
            $types,
            $params
        );

        $content_view = __DIR__ . '/../views/subjects/list.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
