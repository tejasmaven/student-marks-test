<?php

class GradesController
{
    public function handle(string $page): void
    {
        $db = db();
        $grades = query_all($db, 'SELECT grade_id FROM grades ORDER BY grade_id');
        $content_view = __DIR__ . '/../views/grades/list.php';
        require __DIR__ . '/../layout/layout.php';
    }
}
