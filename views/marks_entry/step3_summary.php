<div class="card shadow-sm">
    <div class="card-header bg-white">Step 3: Summary</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Pass</div>
                    <h4><?php echo e((string)($summary['pass_count'] ?? 0)); ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Fail</div>
                    <h4><?php echo e((string)($summary['fail_count'] ?? 0)); ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Average</div>
                    <h4><?php echo e(number_format((float)($summary['avg_mark'] ?? 0), 2)); ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Missing Entries</div>
                    <h4><?php echo e((string)$missing); ?></h4>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <a class="btn btn-primary" href="index.php?page=marks_entry">Enter Another</a>
        </div>
    </div>
</div>
