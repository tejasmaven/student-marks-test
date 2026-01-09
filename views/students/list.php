<div class="d-flex justify-content-between mb-3">
    <form class="row g-2" method="get" action="index.php">
        <input type="hidden" name="page" value="students">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name" value="<?php echo e($_GET['search'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="grade_id" class="form-select form-select-sm">
                <option value="">All Grades</option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo ((int)($_GET['grade_id'] ?? 0) === (int)$grade['grade_id']) ? 'selected' : ''; ?>>Grade <?php echo e((string)$grade['grade_id']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary btn-sm">Filter</button>
        </div>
    </form>
    <a class="btn btn-primary btn-sm" href="index.php?page=students&action=create">Add Student</a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">Students</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Grade</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo e($student['name']); ?></td>
                        <td><?php echo e((string)$student['grade_id']); ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="index.php?page=students&action=edit&id=<?php echo e((string)$student['student_id']); ?>">Edit</a>
                            <form method="post" action="index.php?page=students&action=delete" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="student_id" value="<?php echo e((string)$student['student_id']); ?>">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this student and related marks?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination pagination-sm">
                <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                    <li class="page-item <?php echo $p === $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=students&grade_id=<?php echo e($_GET['grade_id'] ?? ''); ?>&search=<?php echo e($_GET['search'] ?? ''); ?>&p=<?php echo e((string)$p); ?>"><?php echo e((string)$p); ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>
