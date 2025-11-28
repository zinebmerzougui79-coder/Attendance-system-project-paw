# Student Attendance Management System

## Description
A complete PHP/MySQL web application for managing student attendance with beautiful UI.

## Features
- ✅ Add students to database
- ✅ Take attendance with session management  
- ✅ Track participation
- ✅ Generate reports and statistics
- ✅ Responsive design
- ✅ Secure logout system

## Technologies Used
- PHP 7.4+
- MySQL
- HTML5/CSS3
- JavaScript (jQuery)
- Chart.js for analytics

## Installation
1. Import `database_schema.sql` to MySQL
2. Update `config.php` with your database credentials
3. Upload files to your web server
4. Access via browser

## Database Structure
- students (id, matricule, firstname, lastname, course)
- attendance_records (student_id, session_id, status)
- participation_records (student_id, session_id, status) 
- sessions (id, course_name, group_name, status)
- courses (id, name, description)

## Tutorial Exercises Completed
- ✅ Exercise 1: Add student with JSON version
- ✅ Exercise 2: Take attendance with JSON files  
- ✅ Exercise 3: Database connection
- ✅ Exercise 4: CRUD operations with MySQL
- ✅ Exercise 5: Session management