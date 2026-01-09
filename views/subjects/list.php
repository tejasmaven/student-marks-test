<div class="d-flex justify-content-between mb-3">
    <form class="d-flex" method="get" action="index.php">
        <input type="hidden" name="page" value="subjects">
        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search" value="<?php echo e($_GET['search'] ?? ''); ?>">
        <button class="btn btn-outline-secondary btn-sm">Filter</button>
    </form>
    <a class="btn btn-primary btn-sm" href="index.php?page=subjects&action=create">Add Subject</a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">Subjects</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?php echo e($subject['code']); ?></td>
                        <td><?php echo e($subject['name']); ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="index.php?page=subjects&action=edit&id=<?php echo e((string)$subject['subject_id']); ?>">Edit</a>
                            <form method="post" action="index.php?page=subjects&action=delete" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="subject_id" value="<?php echo e((string)$subject['subject_id']); ?>">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this subject?')">Delete</button>
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
                        <a class="page-link" href="index.php?page=subjects&search=<?php echo e($_GET['search'] ?? ''); ?>&p=<?php echo e((string)$p); ?>"><?php echo e((string)$p); ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>
