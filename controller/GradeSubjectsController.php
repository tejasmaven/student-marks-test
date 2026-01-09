<?php

class GradeSubjectsController
{
    public function handle(string $page): void
    {
        $db = db();
        $action = $_GET['action'] ?? 'list';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validate_csrf();
        }

        if ($action === 'map' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            $subjects = $_POST['subject_ids'] ?? [];
            if ($gradeId < 1 || $gradeId > 10) {
                set_flash('danger', 'Please select a valid grade.');
                redirect('index.php?page=grade_subjects');
            }
            if (!is_array($subjects) || empty($subjects)) {
                set_flash('danger', 'Please select at least one subject.');
                redirect('index.php?page=grade_subjects&grade_id=' . $gradeId);
            }
            foreach ($subjects as $subjectId) {
                $subjectId = (int)$subjectId;
                execute_stmt($db, 'INSERT IGNORE INTO grade_subjects (grade_id, subject_id) VALUES (?, ?)', 'ii', [$gradeId, $subjectId]);
            }
            set_flash('success', 'Mappings updated successfully.');
            redirect('index.php?page=grade_subjects&grade_id=' . $gradeId);
        }

        if ($action === 'remove' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $gradeId = (int)($_POST['grade_id'] ?? 0);
            $subjectId = (int)($_POST['subject_id'] ?? 0);

            $hasMarks = query_one(
                $db,
                'SELECT 1 FROM marks m JOIN students s ON m.student_id = s.student_id WHERE s.grade_id = ? AND m.subject_id = ? LIMIT 1',
                'ii',
                [$gradeId, $subjectId]
            );
            if ($hasMarks) {
                set_flash('danger', 'Cannot remove mapping because marks exist for this subject and grade.');
                redirect('index.php?page=grade_subjects&grade_id=' . $gradeId);
            }
            execute_stmt($db, 'DELETE FROM grade_subjects WHERE grade_id = ? AND subject_id = ?', 'ii', [$gradeId, $subjectId]);
            set_flash('success', 'Mapping removed.');
            redirect('index.php?page=grade_subjects&grade_id=' . $gradeId);
        }

        $gradeId = (int)($_GET['grade_id'] ?? 0);
        $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');
        $subjects = query_all($db, 'SELECT * FROM subjects ORDER BY name');
        $mappedSubjects = [];
        if ($gradeId) {
            $mappedSubjects = query_all(
                $db,
                'SELECT s.subject_id, s.code, s.name FROM grade_subjects gs JOIN subjects s ON gs.subject_id = s.subject_id WHERE gs.grade_id = ? ORDER BY s.name',
                'i',
                [$gradeId]
            );
        }

        $content_view = __DIR__ . '/../views/grade_subjects/list.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
