<div class="card shadow-sm">
    <div class="card-header bg-white">Grades</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo e((string)$grade['grade_id']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted">Grades are pre-seeded (1-10) and managed via the database seed.</p>
    </div>
</div>
