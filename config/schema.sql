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

INSERT INTO grades (grade_id) VALUES
(1),(2),(3),(4),(5),(6),(7),(8),(9),(10);

INSERT INTO exams (academic_year, exam_no, exam_name) VALUES
(2026, 1, 'Exam 1'),
(2026, 2, 'Exam 2'),
(2026, 3, 'Exam 3'),
(2026, 4, 'Exam 4');
