<?php
$isEdit = isset($exam);
$actionUrl = $isEdit ? 'index.php?page=exams&action=update&academic_year=' . $academicYear : 'index.php?page=exams&action=create&academic_year=' . $academicYear;
?>
<div class="card shadow-sm">
    <div class="card-header bg-white"><?php echo $isEdit ? 'Edit Exam' : 'Add Exam'; ?></div>
    <div class="card-body">
        <form method="post" action="<?php echo e($actionUrl); ?>">
            <?php echo csrf_field(); ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="exam_id" value="<?php echo e((string)$exam['exam_id']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Exam No (1-4)</label>
                <input type="number" name="exam_no" min="1" max="4" class="form-control" value="<?php echo e((string)($exam['exam_no'] ?? '')); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Exam Name</label>
                <input type="text" name="exam_name" class="form-control" value="<?php echo e($exam['exam_name'] ?? ''); ?>" required>
            </div>
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-outline-secondary" href="index.php?page=exams&academic_year=<?php echo e((string)$academicYear); ?>">Cancel</a>
        </form>
    </div>
</div>
