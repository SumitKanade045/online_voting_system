🗳️ Online Voting System
This is a PHP-based Online Voting System project that allows an admin to manage candidates and voters while users (students) can vote securely. The system uses XAMPP for local server setup and MySQL for database operations.

📁 Project Structure
Project folder: online_voting_system
Main language: PHP
Database: MySQL (college_src_voting.sql)
Server: XAMPP (Apache + MySQL)
🖥️ How to Run the Project
✅ Step 1: Install Requirements
Download and install XAMPP
Make sure Apache and MySQL modules are running in XAMPP Control Panel
✅ Step 2: Move Project Folder
Copy the online_voting_system folder
Paste it inside C:\xampp\htdocs\
So your project path should look like:

✅ Step 3: Import the Database
Open your browser and go to:
http://localhost/phpmyadmin

Click "New" (left sidebar) → Enter database name: college_src_voting → Click Create

Click the college_src_voting database from the sidebar

Click the Import tab (top menu)

Click Choose File → Select your file: college_src_voting.sql

Click Go at the bottom

✅ The database will be imported successfully.

✅ Step 4: Update Database Configuration (Optional)
Open the file includes/config.php or config.php (based on your setup) and make sure your DB credentials are like this:

<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "college_src_voting";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
Run The Project: http://localhost/online_voting_system/
admin username is 123456789012
password is Sumit045