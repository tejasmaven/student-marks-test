<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

$selectedYear = $_GET['academic_year'] ?? DEFAULT_ACADEMIC_YEAR;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(APP_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="layout/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold"><?php echo e(APP_NAME); ?></span>
        <div class="d-flex align-items-center">
            <form class="d-flex align-items-center" method="get" action="index.php">
                <input type="hidden" name="page" value="<?php echo e($_GET['page'] ?? 'dashboard'); ?>">
                <label class="text-white me-2">Academic Year</label>
                <select name="academic_year" class="form-select form-select-sm me-3" onchange="this.form.submit()">
                    <?php for ($year = DEFAULT_ACADEMIC_YEAR - 2; $year <= DEFAULT_ACADEMIC_YEAR + 2; $year++): ?>
                        <option value="<?php echo e((string)$year); ?>" <?php echo ((int)$selectedYear === $year) ? 'selected' : ''; ?>><?php echo e((string)$year); ?></option>
                    <?php endfor; ?>
                </select>
                <input type="text" class="form-control form-control-sm" name="q" placeholder="Global search" value="<?php echo e($_GET['q'] ?? ''); ?>">
                <button class="btn btn-light btn-sm ms-2" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
