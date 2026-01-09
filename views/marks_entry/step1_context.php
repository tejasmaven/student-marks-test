<div class="card shadow-sm">
    <div class="card-header bg-white">Step 1: Select Context</div>
    <div class="card-body">
        <form method="post" action="index.php?page=marks_entry&step=2&academic_year=<?php echo e((string)$academicYear); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Grade</label>
                    <select name="grade_id" class="form-select" required onchange="window.location='index.php?page=marks_entry&grade_id=' + this.value + '&academic_year=<?php echo e((string)$academicYear); ?>'">
                        <option value="">Select grade</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?php echo e((string)$grade['grade_id']); ?>" <?php echo $gradeId === (int)$grade['grade_id'] ? 'selected' : ''; ?>>Grade <?php echo e((string)$grade['grade_id']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">Select subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo e((string)$subject['subject_id']); ?>"><?php echo e($subject['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Exam</label>
                    <select name="exam_id" class="form-select" required>
                        <option value="">Select exam</option>
                        <?php foreach ($exams as $exam): ?>
                            <option value="<?php echo e((string)$exam['exam_id']); ?>">Exam <?php echo e((string)$exam['exam_no']); ?> - <?php echo e($exam['exam_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Continue</button>
            </div>
        </form>
    </div>
</div>
