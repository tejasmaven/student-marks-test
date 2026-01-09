<?php
$isEdit = isset($subject);
$actionUrl = $isEdit ? 'index.php?page=subjects&action=update' : 'index.php?page=subjects&action=create';
?>
<div class="card shadow-sm">
    <div class="card-header bg-white"><?php echo $isEdit ? 'Edit Subject' : 'Add Subject'; ?></div>
    <div class="card-body">
        <form method="post" action="<?php echo e($actionUrl); ?>">
            <?php echo csrf_field(); ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="subject_id" value="<?php echo e((string)$subject['subject_id']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="<?php echo e($subject['code'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($subject['name'] ?? ''); ?>" required>
            </div>
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-outline-secondary" href="index.php?page=subjects">Cancel</a>
        </form>
    </div>
</div>
