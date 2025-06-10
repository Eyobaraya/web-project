-- Drop existing tables safely
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','admin','employer','teacher') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create projects table with foreign key
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (name, email, password, role) VALUES
  ('Alice Admin', 'admin@example.com', 'admin123', 'admin'),
  ('Eve Employer', 'eve@example.com', 'eve123', 'employer'),
  ('Sam Student', 'sam@example.com', 'sam123', 'student');

-- Insert sample projects
INSERT INTO projects (title, description, image, user_id) VALUES
  ('Smart Garden', 'An IoT-based smart garden system.', 'garden.jpg', 3),
  ('Resume Website', 'A student portfolio website project.', 'resume.jpg', 3);
