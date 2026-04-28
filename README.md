 -Document-Management-System-DMS-
A web-based platform for securely uploading, reviewing, managing, and retrieving documents.

---

 📌 Features

 ✅ Core Functionality
- **User Roles**: Admin, Moderator, Regular User
- **Secure Login & Registration**
- **Upload PDFs**: Simple form with validation
- **File Management**: Grid/Table view, filtering, search
- **Review Workflow**: Moderators/Admins approve/reject files with remarks
- **Real-time Feedback**: Status badges and success alerts
- **Audit Logs**: Admin dashboard tracks key actions

⚙️ Admin Capabilities
- Manage users (promote, demote, delete)
- Review all uploads
- Access activity logs and file charts
- Modify categories

 🔐 Security
- Role-based access control
- File access protection (only owners or authorized roles)
- Admin override for file deletions and reviews

----------------------

 💻 Tech Stack

| Technology | Description                         |
|------------|-------------------------------------|
| PHP        | Backend scripting                   |
| MySQL      | Database                            |
| HTML/CSS   | Frontend structure and styling      |
| Bootstrap  | UI Components and Responsive Design |
| JavaScript | Modal control & interactivity       |

----------------------

 🚀 Installation & Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-repo/tschi-dms.git
2. Import the database (SQL file not included here):
3. Create a database (e.g., tschi_dms)
4. Import the provided tschi_dms.sql
5. Configure database settings in config.php:
6. Edit $conn = new mysqli("localhost", "root", "", "tschi_dms");
7. Ensure folders are writable:
   - uploads/
   - elems/profile-picture/
8. Run locally:
9. Open http://localhost/tschi_dms in your browser.
----------------------
 👤 Developers

This system is developed by:
- Cesar Janell Medina – Lead Developer
- Edmund Sealtiel De Veyra – Backend & Security
- Sheila Mae Comandao – UI/UX & Database Design
----------------------
📄 License
This project is developed for educational purposes, and its not open-source for redistribution without permission.
