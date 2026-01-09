<?php
$current = $_GET['page'] ?? 'dashboard';
$items = [
    'dashboard' => ['label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
    'grades' => ['label' => 'Grades'],
    'subjects' => ['label' => 'Subjects'],
    'exams' => ['label' => 'Exams'],
    'grade_subjects' => ['label' => 'Grade → Subjects Mapping'],
    'students' => ['label' => 'Students'],
    'marks_entry' => ['label' => 'Marks Entry'],
    'reports_student' => ['label' => 'Report: Student Card'],
    'reports_grade_summary' => ['label' => 'Report: Grade Summary'],
    'reports_subject_performance' => ['label' => 'Report: Subject Performance'],
    'reports_missing_marks' => ['label' => 'Report: Missing Marks'],
];
?>
<nav class="nav flex-column p-3">
    <a class="nav-link <?php echo $current === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">Dashboard</a>
    <h6 class="text-uppercase text-muted small">Masters</h6>
    <a class="nav-link <?php echo $current === 'grades' ? 'active' : ''; ?>" href="index.php?page=grades">Grades</a>
    <a class="nav-link <?php echo $current === 'subjects' ? 'active' : ''; ?>" href="index.php?page=subjects">Subjects</a>
    <a class="nav-link <?php echo $current === 'exams' ? 'active' : ''; ?>" href="index.php?page=exams">Exams</a>
    <a class="nav-link <?php echo $current === 'grade_subjects' ? 'active' : ''; ?>" href="index.php?page=grade_subjects">Grade → Subjects Mapping</a>

    <h6 class="text-uppercase text-muted small mt-4">Academic</h6>
    <a class="nav-link <?php echo $current === 'students' ? 'active' : ''; ?>" href="index.php?page=students">Students</a>
    <a class="nav-link <?php echo $current === 'marks_entry' ? 'active' : ''; ?>" href="index.php?page=marks_entry">Marks Entry</a>

    <h6 class="text-uppercase text-muted small mt-4">Reports</h6>
    <a class="nav-link <?php echo $current === 'reports_student' ? 'active' : ''; ?>" href="index.php?page=reports_student">Student Report Card</a>
    <a class="nav-link <?php echo $current === 'reports_grade_summary' ? 'active' : ''; ?>" href="index.php?page=reports_grade_summary">Grade Summary</a>
    <a class="nav-link <?php echo $current === 'reports_subject_performance' ? 'active' : ''; ?>" href="index.php?page=reports_subject_performance">Subject Performance</a>
    <a class="nav-link <?php echo $current === 'reports_missing_marks' ? 'active' : ''; ?>" href="index.php?page=reports_missing_marks">Missing Marks</a>
</nav>
