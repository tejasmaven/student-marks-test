<div class="d-flex justify-content-between mb-3">
    <div>
        <a class="btn btn-primary btn-sm" href="index.php?page=exams&action=create&academic_year=<?php echo e((string)$academicYear); ?>">Add Exam</a>
        <form method="post" action="index.php?page=exams&action=generate&academic_year=<?php echo e((string)$academicYear); ?>" class="d-inline">
            <?php echo csrf_field(); ?>
            <button class="btn btn-outline-secondary btn-sm">Generate 4 exams for this year</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">Exams (<?php echo e((string)$academicYear); ?>)</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Exam No</th>
                    <th>Exam Name</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?php echo e((string)$exam['exam_no']); ?></td>
                        <td><?php echo e($exam['exam_name']); ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="index.php?page=exams&action=edit&id=<?php echo e((string)$exam['exam_id']); ?>&academic_year=<?php echo e((string)$academicYear); ?>">Edit</a>
                            <form method="post" action="index.php?page=exams&action=delete&academic_year=<?php echo e((string)$academicYear); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="exam_id" value="<?php echo e((string)$exam['exam_id']); ?>">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this exam?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted">Exactly four exams are enforced per academic year.</p>
    </div>
</div>
