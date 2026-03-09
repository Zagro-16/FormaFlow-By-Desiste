USE formaflow;

-- Password per tutti gli utenti demo: Password123!
INSERT INTO users (full_name,email,password_hash,role) VALUES
('Admin Demo','admin@formaflow.test','$2y$12$rWFABFk0lXWMiG77qdioY.9BGNOPpRAfQnz8bvu/uUVJ9H8/VZn3y','admin'),
('Docente Demo','docente@formaflow.test','$2y$12$rWFABFk0lXWMiG77qdioY.9BGNOPpRAfQnz8bvu/uUVJ9H8/VZn3y','docente'),
('Corsista Demo','corsista@formaflow.test','$2y$12$rWFABFk0lXWMiG77qdioY.9BGNOPpRAfQnz8bvu/uUVJ9H8/VZn3y','corsista');

INSERT INTO teacher_profiles (user_id,bio,total_assigned_hours,total_completed_hours)
VALUES (2,'Docente area informatica',40,16);

INSERT INTO student_profiles (user_id,phone)
VALUES (3,'333000111');

INSERT INTO courses (title,description,start_date,end_date,status,teacher_id,total_hours)
VALUES ('Corso Web Base','HTML CSS JS','2026-01-10','2026-03-10','active',2,40);

INSERT INTO lessons (course_id,title,lesson_date,start_time,end_time,status,google_meet_link) VALUES
(1,'Introduzione HTML','2026-01-12','09:00:00','13:00:00','scheduled','https://meet.google.com/demo-html'),
(1,'CSS Responsive','2026-01-14','09:00:00','13:00:00','scheduled','https://meet.google.com/demo-css');

INSERT INTO enrollments (course_id,student_id,status)
VALUES (1,3,'active');

INSERT INTO settings (setting_key,setting_value) VALUES
('ente_nome','FormaFlow By Desiste'),
('email_from','noreply@formaflow.test');
