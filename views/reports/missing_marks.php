<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="reports_missing_marks">
            <div class="col-md-4">
                <label class="form-label">Grade (optional)</label>
                <select name="grade_id" class="form-select">
                    <option value="">All Grades</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo $gradeId === (int)$grade['grade_id'] ? 'selected' : ''; ?>>Grade <?php echo e((string)$grade['grade_id']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">View</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">Missing Marks (Total Missing: <?php echo e((string)$missingTotal); ?>)</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Grade</th>
                    <th>Subject</th>
                    <th>Exam</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($missingRows as $row): ?>
                    <tr>
                        <td><?php echo e($row['name']); ?></td>
                        <td><?php echo e((string)$row['grade_id']); ?></td>
                        <td><?php echo e($row['subject_name']); ?></td>
                        <td>Exam <?php echo e((string)$row['exam_no']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
