# AlumniConnect

AlumniConnect is a robust PHP-based web application designed to help graduates stay connected with their alma mater and fellow alumni. It bridges the gap between past graduates and current opportunities by providing a dynamic platform for networking, mentoring, and community building.

## 🚀 Features

- **User Authentication:** Secure registration and login for both alumni and administrators.
- **Alumni Directory:** Easily search and find fellow alumni by department, batch year, or current company.
- **Job Opportunities:** A dedicated job board where alumni can post and discover career opportunities within their network.
- **Events & Meetups:** Stay updated with upcoming reunions, seminars, networking events, and webinars.
- **Real-time Chat:** Connect and communicate directly with other alumni through the integrated messaging system.
- **Dashboards:** Dedicated user dashboard (`dashboard.php`) for personal updates and an admin dashboard (`admin_dashboard.php`) for managing users, events, and jobs.
- **College Selection:** Multi-college support allowing users to identify and connect within their specific institution (`select_college.php`).

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap (UI framework & responsive design)
- **Backend:** PHP
- **Database:** MySQL
- **Icons & Fonts:** FontAwesome & Google Fonts

## 📁 Project Structure

```text
Alumni-project/
├── assets/                 # CSS, JavaScript, and Image assets
├── config/                 # Configuration files (e.g., database connection)
├── includes/               # Reusable view components (header, footer, auth logic)
├── admin_dashboard.php     # Dashboard for platform administrators
├── chat.php                # Messaging interface
├── dashboard.php           # User/Alumni dashboard
├── directory.php           # Searchable alumni directory
├── index.php               # Main landing page
├── login.php               # User login page
├── register.php            # User registration page
├── select_college.php      # College selection module
├── alumni_db.sql           # Database schema
└── README.md               # Project documentation
```

## ⚙️ Installation & Setup

1. **Prerequisites**
   Ensure you have a local web server environment installed (such as XAMPP, WAMP, or MAMP) and running PHP and MySQL.

2. **Clone or Download the Repository**
   Move the `Alumni-project` folder to your web server's root directory:
   - For XAMPP: `C:\xampp\htdocs\Alumni-project`
   - For WAMP: `C:\wamp\www\Alumni-project`

3. **Database Configuration**
   - Open your MySQL administration tool (e.g., phpMyAdmin).
   - Create a new database.
   - Import the `alumni_db.sql` file provided in the root directory into your newly created database.

4. **Update Connection Settings**
   - Navigate to the `config/` directory.
   - Open the database connection file (e.g., `db.php`) and update the database credentials (host, username, password, optionally database name) to match your local setup.

5. **Launch the Application**
   - Open your web browser and navigate to `http://localhost/Alumni-project/`.

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the issues page if you want to contribute.

## 📜 License

This project is open-source and available under the MIT License.
