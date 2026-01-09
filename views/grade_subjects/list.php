<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="grade_subjects">
            <div class="col-md-4">
                <label class="form-label">Select Grade</label>
                <select name="grade_id" class="form-select">
                    <option value="">Choose grade</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo $gradeId === (int)$grade['grade_id'] ? 'selected' : ''; ?>><?php echo e((string)$grade['grade_id']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<?php if ($gradeId): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">Mapped Subjects</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mappedSubjects as $subject): ?>
                        <tr>
                            <td><?php echo e($subject['code']); ?></td>
                            <td><?php echo e($subject['name']); ?></td>
                            <td class="text-end">
                                <form method="post" action="index.php?page=grade_subjects&action=remove" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="grade_id" value="<?php echo e((string)$gradeId); ?>">
                                    <input type="hidden" name="subject_id" value="<?php echo e((string)$subject['subject_id']); ?>">
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">Add Subjects to Grade <?php echo e((string)$gradeId); ?></div>
        <div class="card-body">
            <form method="post" action="index.php?page=grade_subjects&action=map">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="grade_id" value="<?php echo e((string)$gradeId); ?>">
                <div class="mb-3">
                    <label class="form-label">Select Subjects</label>
                    <select name="subject_ids[]" class="form-select" multiple size="6">
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo e((string)$subject['subject_id']); ?>"><?php echo e($subject['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary">Save Mapping</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">Select a grade to view and manage mapped subjects.</div>
<?php endif; ?>
