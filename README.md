# Student Project Showcase

A web-based platform for students to showcase their projects, allowing employers and teachers to discover and review student work. This application provides a complete project management system with user authentication, project uploads, and search functionality.

## 🚀 Features

- **User Authentication**: Sign up, login, and logout functionality
- **Role-based Access**: Support for students, teachers, employers, and administrators
- **Project Management**: Upload, edit, and delete projects with images
- **Search Functionality**: Search projects by title and description
- **User Profiles**: Edit profile information and view user details
- **Comment System**: Add comments to projects
- **File Upload**: Support for project images and file downloads
- **Responsive Design**: Modern, mobile-friendly interface

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+**: Server-side scripting language
- **MySQL**: Database management system
- **Apache**: Web server (via XAMPP)

### Frontend
- **HTML5**: Markup language
- **CSS3**: Styling and responsive design
- **JavaScript**: Client-side interactivity
- **Bootstrap**: CSS framework (if used in templates)

### Database
- **MySQL**: Relational database
- **MySQLi**: PHP MySQL extension for database connectivity

## 📋 Prerequisites

Before running this project, make sure you have the following installed:

- **XAMPP** (Apache + MySQL + PHP + phpMyAdmin)
  - Download from: https://www.apachefriends.org/
  - Version: 7.4 or higher recommended
- **Web Browser** (Chrome, Firefox, Safari, Edge)

## 🚀 Installation & Setup

### 1. Install XAMPP
1. Download and install XAMPP from the official website
2. Start Apache and MySQL services from XAMPP Control Panel

### 2. Clone/Download Project
1. Clone this repository or download the project files
2. Place the project folder in your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\Web_Project\
   ```

### 3. Database Setup
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create a new database named `student_showcase`
3. Import the database schema:
   - Go to the "Import" tab
   - Select the file: `sql/schema.sql`
   - Click "Go" to import the database structure and sample data

### 4. Configure Database Connection
The database connection is already configured in `includes/db.php` with default XAMPP settings:
- Host: `localhost`
- Username: `root`
- Password: `` (empty)
- Database: `student_showcase`

If you need to modify these settings, edit the `includes/db.php` file.

### 5. Set Permissions
Ensure the `uploads/` directory has write permissions for file uploads.

## 🌐 Accessing the Application

1. Start XAMPP (Apache and MySQL)
2. Open your web browser
3. Navigate to: `http://localhost/Web_Project/`

## 📁 Project Structure

```
Web_Project/
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── profile.css
│   ├── js/
│   │   └── scripts.js
│   └── img/
├── includes/
│   ├── classes/
│   ├── auth.php
│   ├── db.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── sql/
│   └── schema.sql
├── templates/
├── uploads/
├── index.php
├── login.php
├── signup.php
├── project.php
├── profile.php
├── edit_profile.php
├── edit_project.php
├── upload.php
├── download.php
├── add_comment.php
├── delete_project.php
├── logout.php
├── about.php
├── contact.php
├── privacy.php
└── terms.php
```

## 🔧 Configuration

### Database Configuration
Edit `includes/db.php` to modify database connection settings:
```php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'student_showcase';
```

### File Upload Settings
- Maximum file size: Configured in PHP settings
- Allowed file types: Images (jpg, png, gif, etc.)
- Upload directory: `uploads/`

## 🐛 Troubleshooting

### Common Issues

1. **"MySQLi extension is not enabled"**
   - Enable mysqli extension in php.ini
   - Restart Apache in XAMPP

2. **"Connection failed"**
   - Ensure MySQL is running in XAMPP
   - Check database credentials in `includes/db.php`
   - Verify database `student_showcase` exists

3. **File upload errors**
   - Check `uploads/` directory permissions
   - Verify PHP upload settings in php.ini

4. **Page not found (404)**
   - Ensure project is in correct XAMPP htdocs directory
   - Check Apache is running in XAMPP

## 📝 Usage

### For Students
1. Sign up with student role
2. Upload projects with descriptions and images
3. Edit profile information
4. View and manage your projects

### For Employers/Teachers
1. Sign up with appropriate role
2. Browse and search student projects
3. View project details and student profiles
4. Add comments to projects

### For Administrators
1. Access admin features with admin account
2. Manage users and projects
3. Monitor system activity

## 🔒 Security Features

- Password hashing for user authentication
- Session management
- SQL injection prevention
- File upload validation
- Role-based access control

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

**Note**: This application is designed to run on XAMPP and requires Apache, MySQL, and PHP to function properly. Make sure all services are running before accessing the application.
