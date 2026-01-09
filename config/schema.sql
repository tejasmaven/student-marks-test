CREATE DATABASE IF NOT EXISTS school_marks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_marks;

CREATE TABLE grades (
    grade_id TINYINT PRIMARY KEY
);

CREATE TABLE subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    grade_id TINYINT NOT NULL,
    CONSTRAINT fk_students_grade FOREIGN KEY (grade_id) REFERENCES grades(grade_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE grade_subjects (
    grade_id TINYINT NOT NULL,
    subject_id INT NOT NULL,
    PRIMARY KEY (grade_id, subject_id),
    CONSTRAINT fk_grade_subjects_grade FOREIGN KEY (grade_id) REFERENCES grades(grade_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_grade_subjects_subject FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year INT NOT NULL,
    exam_no TINYINT NOT NULL,
    exam_name VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_exams_year_no (academic_year, exam_no),
    CONSTRAINT chk_exam_no CHECK (exam_no BETWEEN 1 AND 4)
);

CREATE TABLE marks (
    mark_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    exam_id INT NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    UNIQUE KEY uq_marks_student_subject_exam (student_id, subject_id, exam_id),
    CONSTRAINT fk_marks_student FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_marks_subject FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_marks_exam FOREIGN KEY (exam_id) REFERENCES exams(exam_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_marks_range CHECK (marks BETWEEN 0 AND 100)
);

CREATE TABLE marks_reporting (
    mark_id INT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    exam_id INT NOT NULL,
    exam_no TINYINT NOT NULL,
    academic_year INT NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    INDEX idx_marks_reporting_student (student_id),
    INDEX idx_marks_reporting_subject (subject_id),
    INDEX idx_marks_reporting_exam (exam_id),
    INDEX idx_marks_reporting_year (academic_year)
);

INSERT INTO marks_reporting (mark_id, student_id, subject_id, exam_id, exam_no, academic_year, marks)
SELECT m.mark_id, m.student_id, m.subject_id, m.exam_id, e.exam_no, e.academic_year, m.marks
FROM marks m
JOIN exams e ON m.exam_id = e.exam_id;

DELIMITER $$

CREATE TRIGGER trg_marks_reporting_insert
AFTER INSERT ON marks
FOR EACH ROW
BEGIN
    INSERT INTO marks_reporting (mark_id, student_id, subject_id, exam_id, exam_no, academic_year, marks)
    SELECT NEW.mark_id, NEW.student_id, NEW.subject_id, NEW.exam_id, e.exam_no, e.academic_year, NEW.marks
    FROM exams e
    WHERE e.exam_id = NEW.exam_id;
END$$

CREATE TRIGGER trg_marks_reporting_update
AFTER UPDATE ON marks
FOR EACH ROW
BEGIN
    UPDATE marks_reporting mr
    JOIN exams e ON e.exam_id = NEW.exam_id
    SET mr.student_id = NEW.student_id,
        mr.subject_id = NEW.subject_id,
        mr.exam_id = NEW.exam_id,
        mr.exam_no = e.exam_no,
        mr.academic_year = e.academic_year,
        mr.marks = NEW.marks
    WHERE mr.mark_id = NEW.mark_id;
END$$

CREATE TRIGGER trg_marks_reporting_delete
AFTER DELETE ON marks
FOR EACH ROW
BEGIN
    DELETE FROM marks_reporting WHERE mark_id = OLD.mark_id;
END$$

DELIMITER ;

CREATE VIEW v_student_marks AS
SELECT student_id, subject_id, exam_no, academic_year, marks
FROM marks_reporting;

CREATE VIEW v_grade_summary AS
SELECT st.grade_id,
       mr.subject_id,
       mr.exam_no,
       mr.academic_year,
       AVG(mr.marks) AS avg_mark,
       SUM(CASE WHEN mr.marks >= 35 THEN 1 ELSE 0 END) AS pass_count,
       SUM(CASE WHEN mr.marks < 35 THEN 1 ELSE 0 END) AS fail_count
FROM marks_reporting mr
JOIN students st ON mr.student_id = st.student_id
GROUP BY st.grade_id, mr.subject_id, mr.exam_no, mr.academic_year;

CREATE VIEW v_grade_student_averages AS
SELECT st.grade_id,
       mr.academic_year,
       st.student_id,
       st.name,
       AVG(mr.marks) AS avg_mark
FROM marks_reporting mr
JOIN students st ON mr.student_id = st.student_id
GROUP BY st.grade_id, mr.academic_year, st.student_id, st.name;

CREATE VIEW v_subject_performance_marks AS
SELECT st.grade_id,
       mr.subject_id,
       mr.exam_no,
       mr.academic_year,
       mr.marks
FROM marks_reporting mr
JOIN students st ON mr.student_id = st.student_id;

CREATE VIEW v_missing_marks AS
SELECT st.student_id,
       st.name AS student_name,
       st.grade_id,
       s.subject_id,
       s.name AS subject_name,
       e.exam_no,
       e.academic_year
FROM students st
JOIN grade_subjects gs ON st.grade_id = gs.grade_id
JOIN subjects s ON gs.subject_id = s.subject_id
JOIN exams e ON 1=1
LEFT JOIN marks_reporting mr
    ON mr.student_id = st.student_id
    AND mr.subject_id = s.subject_id
    AND mr.exam_id = e.exam_id
WHERE mr.mark_id IS NULL;

CREATE VIEW v_expected_marks AS
SELECT g.grade_id,
       e.academic_year,
       (SELECT COUNT(*) FROM students s WHERE s.grade_id = g.grade_id) AS student_count,
       (SELECT COUNT(*) FROM grade_subjects gs WHERE gs.grade_id = g.grade_id) AS subject_count,
       (SELECT COUNT(*) FROM exams ex WHERE ex.academic_year = e.academic_year) AS exam_count,
       (SELECT COUNT(*) FROM students s WHERE s.grade_id = g.grade_id)
           * (SELECT COUNT(*) FROM grade_subjects gs WHERE gs.grade_id = g.grade_id)
           * (SELECT COUNT(*) FROM exams ex WHERE ex.academic_year = e.academic_year) AS expected_count
FROM grades g
CROSS JOIN (SELECT DISTINCT academic_year FROM exams) e;

CREATE VIEW v_actual_marks AS
SELECT st.grade_id,
       mr.academic_year,
       COUNT(*) AS actual_count
FROM marks_reporting mr
JOIN students st ON mr.student_id = st.student_id
GROUP BY st.grade_id, mr.academic_year;

INSERT INTO grades (grade_id) VALUES
(1),(2),(3),(4),(5),(6),(7),(8),(9),(10);

INSERT INTO exams (academic_year, exam_no, exam_name) VALUES
(2026, 1, 'Exam 1'),
(2026, 2, 'Exam 2'),
(2026, 3, 'Exam 3'),
(2026, 4, 'Exam 4');
