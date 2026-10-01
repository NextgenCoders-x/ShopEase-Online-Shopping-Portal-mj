======================================================
SHOPEASE - SETUP AND DEPLOYMENT GUIDE
======================================================

Follow these simple steps to install and run the ShopEase application on any Windows computer using XAMPP.

1. INSTALL XAMPP
   - Download and install XAMPP for Windows from apachefriends.org.
   - Run the XAMPP Control Panel.
   - Click "Start" next to Apache.
   - Click "Start" next to MySQL.

2. COPY THE PROJECT
   - Copy this entire folder (the folder containing this deployment guide, the shopping folder, and the database folder).
   - Paste the folder into your XAMPP htdocs directory.
   - Example path: C:\xampp\htdocs\ShopEase

3. CREATE THE DATABASE
   - Open your web browser.
   - Go to: http://localhost/phpmyadmin
   - Click on "Databases" in the top menu.
   - Under "Create database", enter: shopping
   - Click "Create".

4. IMPORT THE SQL DATA
   - Click on the new "shopping" database on the left sidebar.
   - Click the "Import" tab at the top.
   - Click "Choose File" and select the `shopping.sql` file located in the `database/` folder of this project.
   - Scroll down and click "Import" or "Go". You should see a success message.

5. CONFIGURE DATABASE (Optional)
   - If your MySQL user is not 'root' or has a password, open `shopping/includes/config.php` in a text editor (like Notepad).
   - Update the DB_USER and DB_PASS fields to match your MySQL configuration.

6. OPEN THE WEBSITE
   - Open your web browser.
   - Go to: http://localhost/ShopEase/shopping/
   - (Change 'ShopEase' to whatever you named the folder inside htdocs).

7. ADMIN LOGIN
   - Go to: http://localhost/ShopEase/shopping/admin/
   - Login using the default credentials:
     Username: admin
     Password: Test@123

8. CHANGING ADMIN PASSWORD
   - Once logged into the admin dashboard, click the User icon on the top right.
   - Select "Change Password".
   - Enter your current password and choose a new, secure password.

9. BACKUP AND RESTORE
   - To Backup: Go to phpMyAdmin, select the `shopping` database, click "Export", and save the .sql file. Also copy the `shopping/admin/productimages/` folder to save your uploaded images.
   - To Restore: Import your .sql backup file in phpMyAdmin and paste back the `productimages/` folder.

COMMON ERRORS:
- "Access denied for user": Check `shopping/includes/config.php` and ensure your database username and password are correct.
- "Unknown database": Make sure you created a database named `shopping` and imported the SQL file into it.

Enjoy using ShopEase!
