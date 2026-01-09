ALTER TABLE marks
    ADD COLUMN academic_year INT NULL AFTER exam_id;

UPDATE marks m
JOIN exams e ON m.exam_id = e.exam_id
SET m.academic_year = e.academic_year;

ALTER TABLE marks
    MODIFY academic_year INT NOT NULL,
    ADD INDEX idx_marks_year (academic_year);
