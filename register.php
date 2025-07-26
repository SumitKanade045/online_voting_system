<?php
session_start();
include "config.php";

// Check if registration is allowed
$check = $conn->query("SELECT registration_open FROM election_status WHERE id = 1");
$row = $check->fetch_assoc();

if (!$row || $row['registration_open'] != 1) {
    echo "<script>alert('❌ Registration is currently closed by admin.'); window.location.href='index.php';</script>";
    exit();
}

// Continue with registration logic...


// ✅ Step 2: Proceed if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $aadhar    = trim($_POST['aadhar']);
    $password  = trim($_POST['password']);
    $role      = 'student';

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR aadhar = ?");
    $check->bind_param("ss", $email, $aadhar);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('⚠️ Email or Aadhar already registered.'); window.location.href = 'register.php';</script>";
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, aadhar, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $aadhar, $hashedPassword, $role);

    if ($stmt->execute()) {
        $_SESSION['aadhar'] = $aadhar;
        $_SESSION['role'] = $role;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;

        echo "<script>alert('🎉 Registration successful!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Registration failed. Please try again.'); window.location.href = 'register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .register-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 450px;
    }

    .register-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    .input-group {
      margin-bottom: 18px;
    }

    .input-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #555;
    }

    .input-group input {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .register-button {
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .register-button:hover {
      background-color: #218838;
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2>Create Account</h2>
    <form action="register.php" method="POST">
      <div class="input-group">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" required>
      </div>

      <div class="input-group">
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" required>
      </div>

      <div class="input-group">
        <label for="aadhar">Aadhar Number</label>
        <input type="text" id="aadhar" name="aadhar" pattern="\d{12}" maxlength="12" required>
      </div>

      <div class="input-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" class="register-button">Register</button>
    </form>

    <div class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>

</body>
</html>
