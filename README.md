# Megastar Eid Carnival 2026 – Finance Management System

## 📖 The Story Behind This Project
**"What happened was, for the carnival that took place in our village, the Club Treasurer asked me for an easy method to manage the accounts. That is exactly why I built this web application—customized specifically with the features he requested, bringing a digital solution to solve his real-world problem."**

---

## 🚀 Project Overview
This is a lightweight, fully customized PHP/MySQL web application built specifically to track donations, expenses, and prize sponsors for the **Megastar Eid Carnival 2026**. It replaced traditional paper/Excel accounting with a modern, fast, and easy-to-use digital dashboard. 

It provides a secure **Admin Dashboard** for the Treasurer to insert and manage financial records, alongside a **Viewer Portal** for other committee members to see transparent financial figures without being able to edit them.

## ✨ Key Features
- **📊 Real-time Dashboard**: A clean, card-based UI showing total donations, total expenses, available balance, total donors, and total prizes received.
- **💰 Donation & Expense Tracking**: Easy forms to log who donated what (with areas/phone numbers) and where the money was spent.
- **🎁 Prize Sponsors Management**: Tracks sponsors, the specific items they are sponsoring, and the exact quantity (`prize_count`).
- **🌓 Dark / Light Mode Toggle**: A modern, seamless theme switch that remembers the user's preference using `localStorage`—without reloading the page.
- **👀 Public Viewer Access**: A read-only portal (`viewer.php`) to ensure financial transparency for the club members.
- **📱 Fully Responsive UI**: Works perfectly on mobile phones, making it easy for the Treasurer to update records on the go.

## 🛠️ Technology Stack
- **Frontend**: HTML5, CSS3 (Custom CSS with CSS Variables for smooth theming), Vanilla JavaScript.
- **Backend**: PHP 8.x
- **Database**: MySQL (using PDO for secure queries and protection against SQL injection)
- **Icons**: FontAwesome

## 📂 Core File Structure
```text
/
├── dashboard.php         # Main Admin dashboard showing summaries & recent activities
├── donations.php         # Page to view, add, edit, and delete donations
├── expenses.php          # Page to view, add, edit, and delete expenses
├── prize_sponsors.php    # Page to manage prize sponsors & item quantities
├── reports.php           # Detailed reports generator
├── viewer.php            # Read-only dashboard for transparency
├── index.php             # Main entry / redirect logic
├── login.php             # Secure login area
├── logout.php            # Session destruction
├── db.php                # PDO Database connection logic
├── database.sql          # Database schema and initial setup queries
├── css/
│   └── style.css         # All UI styling, responsive grids, and Dark/Light mode variables
└── includes/
    ├── auth.php          # Session and authentication checks
    ├── header.php        # Top navigation and Dark mode toggle logic
    ├── sidebar.php       # Admin side navigation
    └── footer.php        # Footer scripts
```

## ⚙️ How to Install and Run Locally

1. **Pre-requisites**: Install a local server environment like XAMPP, WAMP, or Laragon.
2. **Clone/Copy Files**: Place the project folder (`Megastar_Application`) into your server's root directory (e.g., `htdocs` for XAMPP).
3. **Database Setup**:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Create a new database named `megastar_carnival`.
   - Import the `database.sql` file into this new database.
4. **Configuration**:
   - Open `db.php` and verify the database connection settings (hostname, dbname, username, password).
## 🌐 Live Demo & Access

**Live Application**: [https://megastar.page.gd/index.php](https://megastar.page.gd/index.php)

### 🔐 Credentials (Live & Local)
- **Admin Username**: `MGsecratery`
- **Admin Password**: `Megastar2026EidCarnival`

5. **Access the App (Local)**:
   - Open your browser and go to `http://localhost/Megastar_Application/login.php`


---
*Developed with ❤️ to make community events easier to manage.*

🔗 **Connect with me:** [My LinkedIn Profile](https://www.linkedin.com/in/riham-hanifa/)

