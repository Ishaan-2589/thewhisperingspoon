# 🍽️ The Whispering Spoon

A premium, full-stack luxury restaurant management system and customer-facing web application. Built with PHP, MySQL, and vanilla JavaScript, this project simulates a complete commercial restaurant workflow—from a customer browsing the menu to the chef cooking the food, and the manager analyzing monthly sales.

![Project Banner](assets/images/others/banner.png)

---

## ✨ Key Features

### 👤 Customer Experience (Frontend)
* **Interactive Menu:** Browse dishes with real-time filtering (by category or Veg/Non-veg toggle) and CSS-animated staggered rendering.
* **Smart Cart & Checkout:** Add items to cart, select delivery options, simulate secure online payments, and add custom dietary/special requests.
* **Order History & One-Click Reorder:** View past orders and instantly re-populate the cart with a single click.
* **PDF Invoices:** Dynamically generate and download beautifully formatted paper-style receipts using `html2pdf.js`.
* **Table Reservations:** Book tables for specific dates/times, view live status updates, and cancel upcoming bookings via a custom dark-mode UI modal.
* **Reviews & Ratings:** View detailed dish pages and leave 1-to-5 star reviews with comments.
* **Profile Management:** Update personal details, default delivery addresses, and securely change passwords.
* **Email Integration:** Fully functional Contact form that sends real HTML emails via SMTP (PHPMailer).

### 👨‍🍳 Admin & Kitchen Operations (Backend)
* **Live Kitchen Display System (KDS):** A real-time, auto-syncing board for chefs. Orders move from "New" ➡️ "Cooking" ➡️ "Ready". Features live wait-time tracking and flashing red warnings for allergy/special requests.
* **Sales Analytics Dashboard:** Time-travel reporting! Filter by month and year to view total revenue, items sold, and a visual Bar Chart (via `Chart.js`) of the Top 5 best-selling dishes.
* **Order & Booking Management:** Manually confirm/decline table reservations and track all incoming food orders.
* **Master Kill Switch:** A global "Store Settings" toggle to open or close the kitchen. Closing the store automatically disables the checkout button for all users.

---

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3 (Custom Luxury Dark/Gold Theme), Vanilla JavaScript
* **Backend:** PHP 8+ (Session-based authentication, Native routing)
* **Database:** MySQL (Relational database with cascading foreign keys)
* **Libraries/APIs:** * `Chart.js` (Sales Analytics)
  * `html2pdf.js` (Receipt Generation)
  * `PHPMailer` (SMTP Email Gateway)
  * FontAwesome (Icons)

---

## 🚀 Installation & Setup

* To run this project locally on your machine, you will need a local server environment like **XAMPP**, **WAMP**, or **MAMP**.

### 1. Clone the Repository
```bash
git clone [https://github.com/Ishaan-2589/thewhisperingspoon.git](https://github.com/Ishaan-2589/thewhisperingspoon.git)
```
* Move the cloned folder into your local server's root directory (e.g., C:/xampp/htdocs/TheWhisperingSpoon).

### 2. Database Setup
* Open phpMyAdmin (http://localhost/phpmyadmin).

* Create a new database named exactly: thewhisperingspoon.

* Import the provided SQL file (if you exported one, e.g., database.sql) into this new database to instantly create all tables (users, menu_items, orders, order_items, bookings, reviews, settings).

### 3. Configuration
* Open /includes/db.php.

* Ensure the database credentials match your local setup:

* PHP
$host = "localhost";
$user = "root";       // Default XAMPP user
$password = "";       // Default XAMPP password (blank)
$dbname = "thewhisperingspoon";

### 4. Email Configuration (Optional)
* To enable the Contact form to send real emails:

* Open /includes/mailer.php.

* Replace the placeholder Gmail address and 16-digit App Password with your own credentials.

### 5. Run the App
* Open your browser and navigate to: http://localhost/TheWhisperingSpoon/public/index.php

## 🔒 Security Hardening (Production Ready)
* This application includes several production-ready security measures:

* Password Hashing: All user passwords are encrypted using PHP's native password_hash() (Bcrypt).

* Prepared Statements: 100% of database queries use mysqli_prepare and bound parameters to prevent SQL Injection.

* Directory Protection: Included .htaccess file prevents directory listing and protects sensitive include files.

* Error Silencing: PHP errors are disabled in db.php to prevent path disclosure attacks on live servers.

## 👨‍💻 Author
* [Ishaan] * GitHub: @Ishaan-2589

* LinkedIn: www.linkedin.com/in/ishaan-bcajims

If you like this project, please consider giving it a ⭐!
