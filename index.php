<?php
/*
README
1) Import the database schema from /config/schema.sql into MySQL 8.0+.
2) Update database credentials in /config/db.php.
3) Run the app with PHP built-in server: php -S localhost:8000
   or use Apache with document root pointing to this folder.
*/

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$page = $_GET['page'] ?? 'dashboard';

$controllerMap = [
    'dashboard' => 'DashboardController',
    'grades' => 'GradesController',
    'subjects' => 'SubjectsController',
    'exams' => 'ExamsController',
    'grade_subjects' => 'GradeSubjectsController',
    'students' => 'StudentsController',
    'marks_entry' => 'MarksEntryController',
    'reports_student' => 'ReportsController',
    'reports_grade_summary' => 'ReportsController',
    'reports_subject_performance' => 'ReportsController',
    'reports_missing_marks' => 'ReportsController',
];

if (!isset($controllerMap[$page])) {
    set_flash('danger', 'Unknown page requested.');
    $page = 'dashboard';
}

$controllerName = $controllerMap[$page];
require_once __DIR__ . '/controller/' . $controllerName . '.php';

$controller = new $controllerName();
$controller->handle($page);
