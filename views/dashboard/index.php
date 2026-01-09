<?php
$passCount = (int)($passFail['pass_count'] ?? 0);
$failCount = (int)($passFail['fail_count'] ?? 0);
?>
<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Students</h6>
                <h3><?php echo e((string)$stats['students']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Subjects</h6>
                <h3><?php echo e((string)$stats['subjects']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Marks Entered</h6>
                <h3><?php echo e((string)$stats['marks']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Missing Marks</h6>
                <h3><?php echo e((string)$missing); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                Overall Pass/Fail Ratio (<?php echo e((string)$academicYear); ?>)
            </div>
            <div class="card-body">
                <p class="mb-1">Pass: <strong><?php echo e((string)$passCount); ?></strong></p>
                <p class="mb-1">Fail: <strong><?php echo e((string)$failCount); ?></strong></p>
                <div class="progress">
                    <?php
                    $total = max(1, $passCount + $failCount);
                    $passPct = round(($passCount / $total) * 100);
                    ?>
                    <div class="progress-bar bg-success" style="width: <?php echo e((string)$passPct); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Grade Pass/Fail</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Pass</th>
                            <th>Fail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gradePassFail as $row): ?>
                            <tr>
                                <td><?php echo e((string)$row['grade_id']); ?></td>
                                <td><?php echo e((string)($row['pass_count'] ?? 0)); ?></td>
                                <td><?php echo e((string)($row['fail_count'] ?? 0)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Quick Links</div>
            <div class="card-body">
                <a class="btn btn-outline-primary btn-sm" href="index.php?page=students">Manage Students</a>
                <a class="btn btn-outline-primary btn-sm" href="index.php?page=subjects">Manage Subjects</a>
                <a class="btn btn-outline-primary btn-sm" href="index.php?page=marks_entry">Enter Marks</a>
                <a class="btn btn-outline-primary btn-sm" href="index.php?page=reports_student">Student Report</a>
            </div>
        </div>
    </div>
</div>
