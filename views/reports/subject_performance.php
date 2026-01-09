<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="reports_subject_performance">
            <div class="col-md-3">
                <label class="form-label">Grade</label>
                <select name="grade_id" class="form-select" required onchange="window.location='index.php?page=reports_subject_performance&grade_id=' + this.value + '&academic_year=<?php echo e((string)$academicYear); ?>'">
                    <option value="">Select grade</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo $gradeId === (int)$grade['grade_id'] ? 'selected' : ''; ?>>Grade <?php echo e((string)$grade['grade_id']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required>
                    <option value="">Select subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo e((string)$subject['subject_id']); ?>" <?php echo $subjectId === (int)$subject['subject_id'] ? 'selected' : ''; ?>><?php echo e($subject['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">View</button>
            </div>
        </form>
    </div>
</div>

<?php if ($gradeId && $subjectId): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">Distribution Buckets (Year <?php echo e((string)$academicYear); ?>)</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>&lt;35</th>
                        <th>35-44</th>
                        <th>45-60</th>
                        <th>61-80</th>
                        <th>81-100</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 4; $i++):
                        $dist = $distribution[$i] ?? ['<35' => 0, '35-44' => 0, '45-60' => 0, '61-80' => 0, '81-100' => 0];
                    ?>
                        <tr>
                            <td>Exam <?php echo e((string)$i); ?></td>
                            <td><?php echo e((string)$dist['<35']); ?></td>
                            <td><?php echo e((string)$dist['35-44']); ?></td>
                            <td><?php echo e((string)$dist['45-60']); ?></td>
                            <td><?php echo e((string)$dist['61-80']); ?></td>
                            <td><?php echo e((string)$dist['81-100']); ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">Average Trend (Year <?php echo e((string)$academicYear); ?>)</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <tr>
                            <td>Exam <?php echo e((string)$i); ?></td>
                            <td><?php echo e(number_format((float)($trend[$i] ?? 0), 2)); ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">Select grade and subject to view the performance report.</div>
<?php endif; ?>
