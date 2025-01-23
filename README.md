This repository contains the **AUMConnect: Final Year Project Supervision System**, a web application developed to streamline the supervision process for final-year projects. The system integrates features such as appointment scheduling, progress tracking, logbook management, and communication tools to enhance collaboration between students, supervisors, and coordinators.

---

## **Features**

### **Account Management**
- User registration and role selection (Student, Supervisor, Coordinator).
- Secure login and logout functionality.
- Password reset option.
- Profile management, including project preferences.
- Feedback reporting for system improvements.

### **Admin Management**
- User and Feeddback Management of the system

### **Appointment Module**
- Supervisors can create, edit, and delete appointment slots.
- Students can book available slots and request custom appointments.
- Integration with Google Calendar for appointment synchronization and automatic Google Meet link generation.
- Notifications and reminders for appointments.
- Real-time updates for group projects, where bookings are shared across group members.

### **Project Module**
- Supervisors can add, edit, and assign projects to students.
- Task and file management for project timelines.
- Dynamic progress bars and leaderboards based on task completion rates.
- Integration with chatrooms for each project for seamless communication.
- Notifications for overdue tasks.

### **Logbook Module**
- Students can add, edit, and delete logbook entries with supporting documents.
- Automatic synchronization of meeting records from the Appointment Module.
- Supervisors can verify and comment on logbook entries.
- Print-to-PDF functionality for professional-quality logbook exports.

### **Coordinator Module**
- Dashboard with department-specific charts for students, supervisors, and projects.
- Access to appointment and logbook records for all projects.
- Reminder notifications for incomplete records or logbooks.

### **Communication Module**
- Supervisors can create announcements for students.
- Real-time chatrooms for projects using AJAX and JSON for instant updates.
- Editable and deletable messages with notifications.

---

## **Technologies Used**

### **Frontend**
- **HTML, CSS, Bootstrap**: For layout, design, and responsiveness.
- **JavaScript with AJAX and JSON**: For dynamic and real-time functionality.

### **Backend**
- **Laravel Framework**: PHP-based framework for routing, authentication, and backend logic.
- **MySQL**: Relational database for data management.
- **PHP**: Server-side scripting for backend development.

### **APIs**
- **Google Calendar API**: Synchronization of appointments and generation of Google Meet links.
- **SMTP Mail Server**: For sending email notifications.
