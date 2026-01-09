<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="reports_student">
            <div class="col-md-5">
                <label class="form-label">Student</label>
                <select name="student_id" class="form-select" required>
                    <option value="">Select student</option>
                    <?php foreach ($students as $row): ?>
                        <option value="<?php echo e((string)$row['student_id']); ?>" <?php echo $studentId === (int)$row['student_id'] ? 'selected' : ''; ?>><?php echo e($row['name']); ?> (Grade <?php echo e((string)$row['grade_id']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary">View Report</button>
            </div>
        </form>
    </div>
</div>

<?php if ($student): ?>
    <div class="card shadow-sm">
        <div class="card-header bg-white">Report Card: <?php echo e($student['name']); ?></div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <?php foreach ($exams as $exam): ?>
                            <th>Exam <?php echo e((string)$exam['exam_no']); ?></th>
                        <?php endforeach; ?>
                        <th>Total</th>
                        <th>Average</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $overallTotal = 0;
                    $overallCount = 0;
                    foreach ($subjects as $subject):
                        $subjectTotal = 0;
                        $subjectCount = 0;
                        $subjectFail = false;
                    ?>
                        <tr>
                            <td><?php echo e($subject['name']); ?></td>
                            <?php foreach ($exams as $exam):
                                $mark = $marksMap[$subject['subject_id']][$exam['exam_no']] ?? null;
                                if ($mark !== null) {
                                    $subjectTotal += $mark;
                                    $subjectCount++;
                                    $overallTotal += $mark;
                                    $overallCount++;
                                    if ($mark < PASSING_MARK) {
                                        $subjectFail = true;
                                    }
                                }
                            ?>
                                <td><?php echo $mark !== null ? e(number_format((float)$mark, 2)) : '-'; ?></td>
                            <?php endforeach; ?>
                            <td><?php echo e(number_format($subjectTotal, 2)); ?></td>
                            <td><?php echo $subjectCount ? e(number_format($subjectTotal / $subjectCount, 2)) : '-'; ?></td>
                            <td>
                                <?php if ($subjectFail): ?>
                                    <span class="badge bg-danger">Fail</span>
                                <?php elseif ($subjectCount > 0): ?>
                                    <span class="badge bg-success">Pass</span>
                                <?php else: ?>
                                    <span class="text-muted">No data</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="mt-3">
                <strong>Overall Average:</strong> <?php echo $overallCount ? e(number_format($overallTotal / $overallCount, 2)) : 'N/A'; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">Select a student to view their report card.</div>
<?php endif; ?>
