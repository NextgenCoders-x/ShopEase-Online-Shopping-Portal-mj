# ShopEase — Online Shopping Portal

## 1. Project Overview
ShopEase is a complete, modern PHP/MySQL e-commerce portal. It includes both a feature-rich customer storefront and a secure, responsive administrative dashboard to manage products, categories, orders, and users.

## 2. Features
**Customer Facing:**
- Browse, search, and filter products by category.
- Secure user registration and login.
- Shopping cart functionality.
- Wishlist management (Login Required).
- Seamless checkout process with automated order ID generation.
- Order history tracking and status viewing.
- Profile and password management.

**Admin Facing:**
- Modern Dashboard with key metrics (Total orders, sales, users).
- Complete CRUD operations for Categories, Subcategories, and Products.
- Order management (Pending, Dispatched, In-Transit, Delivered, Cancelled).
- User management and login logs tracking.
- Secure admin authentication.

## 3. Technologies
- **Backend:** PHP 8.2+
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3 (Modern, Flexbox/Grid-based), JavaScript (Vanilla)
- **Icons:** Bootstrap Icons, FontAwesome (Admin)

## 4. System Requirements
- OS: Windows / macOS / Linux
- Server Environment: XAMPP, WAMP, or LAMP stack
- PHP Version: 8.0+
- Database: MySQL or MariaDB

## 5. XAMPP Requirements
- Apache HTTP Server
- MySQL/MariaDB

## 6. Installation
**STEP 1:** Install XAMPP on your computer.  
**STEP 2:** Start Apache and MySQL from the XAMPP Control Panel.  
**STEP 3:** Copy this entire project folder into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\`).

## 7. Database Setup
**STEP 4:** Open your web browser and go to `http://localhost/phpmyadmin/`.  
**STEP 5:** Create a new database named `shopping` (Collation: `utf8mb4_general_ci`).  
**STEP 6:** Select the `shopping` database, click on the **Import** tab, and upload the `shopping.sql` file located in the `database/` folder of this project. Click **Go** to import the tables and initial data.

## 8. Configuration
**STEP 7:** Open `shopping/includes/config.php` in a text editor.
Update the credentials if they differ from the defaults:
```php
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASS' ,'');
define('DB_NAME', 'shopping');
```

## 9. Running the Application
**STEP 8:** Open your browser and navigate to:
`http://localhost/YOUR_FOLDER_NAME/shopping/index.php`

## 10. Customer URL
**Storefront:** `http://localhost/YOUR_FOLDER_NAME/shopping/`

## 11. Admin URL
**Admin Portal:** `http://localhost/YOUR_FOLDER_NAME/shopping/admin/`

## 12. Admin Login Setup
- **Default Username:** admin
- **Default Password:** Test@123
*(Note: It is highly recommended to change this password immediately via the Admin Profile/Change Password section after your first login).*

## 13. Product Image Upload Requirements
- Images uploaded via the Admin Portal are saved in `shopping/admin/productimages/{product_id}/`.
- Ensure the `productimages/` directory has proper write permissions on your server.
- The system automatically creates a sub-directory for each new product ID.

## 14. Troubleshooting
- **Database Connection Error:** Verify that your MySQL server is running and that the credentials in `shopping/includes/config.php` are correct.
- **Pages Not Loading:** Ensure you have placed the project folder inside the `htdocs` directory correctly.
- **Admin Login Failed:** The password hash uses PHP's modern `password_hash()`. If you reset it manually in the database, ensure it is generated using `PASSWORD_DEFAULT`.

## 15. Backup & Restore Instructions
- **Backup:** Use phpMyAdmin to export the `shopping` database as a `.sql` file, or use the `mysqldump` utility. Keep copies of your `shopping/admin/productimages/` directory to preserve product photos.
- **Restore:** Create an empty database, import your `.sql` backup file via phpMyAdmin, and restore the `productimages/` directory.

## 16. Security Notes
- Passwords are securely hashed using PHP `password_hash()`.
- Customer checkout and wishlist workflows are strictly protected against guest access.
- Admin directory access is strictly protected.

## 17. Project Structure
```
ShopEase/
├── shopping/               # Main application source code
│   ├── admin/              # Secure administrator portal
│   ├── css/                # Application stylesheets
│   ├── images/             # Static layout images
│   ├── includes/           # Shared PHP components (config, header, footer)
│   └── index.php           # Customer Storefront Homepage
├── database/               # Database files
│   └── shopping.sql        # Initial database structure and seed data
├── deployment/             # Customer installation guides
│   └── README.txt          # Setup Guide
└── README.md               # Main project documentation
```

## 18. Credits / License
Developed as a comprehensive, modern e-commerce solution.
