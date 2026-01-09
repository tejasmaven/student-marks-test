<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="reports_grade_summary">
            <div class="col-md-4">
                <label class="form-label">Grade</label>
                <select name="grade_id" class="form-select" required>
                    <option value="">Select grade</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo $gradeId === (int)$grade['grade_id'] ? 'selected' : ''; ?>>Grade <?php echo e((string)$grade['grade_id']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">View Summary</button>
            </div>
        </form>
    </div>
</div>

<?php if ($gradeId): ?>
    <?php
    $summaryMap = [];
    foreach ($summaryRows as $row) {
        $summaryMap[$row['subject_id']][$row['exam_no']] = $row;
    }
    ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">Grade <?php echo e((string)$gradeId); ?> Summary (Year <?php echo e((string)$academicYear); ?>)</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <th>Exam <?php echo e((string)$i); ?> Avg</th>
                            <th>Pass</th>
                            <th>Fail</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?php echo e($subject['name']); ?></td>
                            <?php for ($i = 1; $i <= 4; $i++):
                                $row = $summaryMap[$subject['subject_id']][$i] ?? null;
                            ?>
                                <td><?php echo $row ? e(number_format((float)$row['avg_mark'], 2)) : '-'; ?></td>
                                <td><?php echo $row ? e((string)$row['pass_count']) : '-'; ?></td>
                                <td><?php echo $row ? e((string)$row['fail_count']) : '-'; ?></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">Top 5 Students (Overall Average, Year <?php echo e((string)$academicYear); ?>)</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topStudents as $row): ?>
                        <tr>
                            <td><?php echo e($row['name']); ?></td>
                            <td><?php echo e(number_format((float)$row['avg_mark'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">Select a grade to view the summary report.</div>
<?php endif; ?>
