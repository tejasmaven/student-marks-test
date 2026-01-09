<div class="card shadow-sm">
    <div class="card-header bg-white">Step 2: Enter Marks</div>
    <div class="card-body">
        <p class="text-muted">Subject: <?php echo e($subject['name'] ?? ''); ?> | Exam: <?php echo e($exam['exam_name'] ?? ''); ?> (Exam <?php echo e((string)($exam['exam_no'] ?? '')); ?>)</p>
        <form method="post" action="index.php?page=marks_entry&step=save&academic_year=<?php echo e((string)$academicYear); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="grade_id" value="<?php echo e((string)$gradeId); ?>">
            <input type="hidden" name="subject_id" value="<?php echo e((string)$subjectId); ?>">
            <input type="hidden" name="exam_id" value="<?php echo e((string)$examId); ?>">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Marks (0-<?php echo e((string)TOTAL_MARKS); ?>)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student):
                        $markValue = $marksMap[$student['student_id']] ?? '';
                        $status = '';
                        $badge = 'secondary';
                        if ($markValue !== '') {
                            $markValueFloat = (float)$markValue;
                            if ($markValueFloat < PASSING_MARK) {
                                $status = 'Fail';
                                $badge = 'danger';
                            } elseif ($markValueFloat == PASSING_MARK) {
                                $status = 'Borderline Pass';
                                $badge = 'warning';
                            } elseif ($markValueFloat <= 60) {
                                $status = 'Average';
                                $badge = 'info';
                            } else {
                                $status = 'Good';
                                $badge = 'success';
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo e($student['name']); ?></td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" name="marks[<?php echo e((string)$student['student_id']); ?>]" class="form-control form-control-sm" value="<?php echo e((string)$markValue); ?>">
                            </td>
                            <td>
                                <?php if ($status): ?>
                                    <span class="badge bg-<?php echo e($badge); ?> status-badge"><?php echo e($status); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Not entered</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-primary">Save Marks</button>
            <a class="btn btn-outline-secondary" href="index.php?page=marks_entry">Back</a>
        </form>
    </div>
</div>
