<?php
$isEdit = isset($student);
$actionUrl = $isEdit ? 'index.php?page=students&action=update' : 'index.php?page=students&action=create';
?>
<div class="card shadow-sm">
    <div class="card-header bg-white"><?php echo $isEdit ? 'Edit Student' : 'Add Student'; ?></div>
    <div class="card-body">
        <form method="post" action="<?php echo e($actionUrl); ?>">
            <?php echo csrf_field(); ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="student_id" value="<?php echo e((string)$student['student_id']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Student Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($student['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Grade</label>
                <select name="grade_id" class="form-select" required>
                    <option value="">Select grade</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo isset($student['grade_id']) && (int)$student['grade_id'] === (int)$grade['grade_id'] ? 'selected' : ''; ?>>Grade <?php echo e((string)$grade['grade_id']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-outline-secondary" href="index.php?page=students">Cancel</a>
        </form>
    </div>
</div>
