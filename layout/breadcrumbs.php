<?php
$breadcrumbMap = [
    'dashboard' => 'Dashboard',
    'grades' => 'Grades',
    'subjects' => 'Subjects',
    'exams' => 'Exams',
    'grade_subjects' => 'Grade → Subjects Mapping',
    'students' => 'Students',
    'marks_entry' => 'Marks Entry',
    'reports_student' => 'Reports / Student Report Card',
    'reports_grade_summary' => 'Reports / Grade Summary',
    'reports_subject_performance' => 'Reports / Subject Performance',
    'reports_missing_marks' => 'Reports / Missing Marks',
];
$current = $_GET['page'] ?? 'dashboard';
$title = $breadcrumbMap[$current] ?? 'Dashboard';
$academicYear = current_academic_year();
if (str_starts_with($current, 'reports_')) {
    $title .= ' (Year ' . $academicYear . ')';
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><?php echo e($title); ?></h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($title); ?></li>
            </ol>
        </nav>
    </div>
</div>
