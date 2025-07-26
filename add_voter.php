<?php
session_start();
include "config.php";

// Only allow admin
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied'); window.location.href = 'login.html';</script>";
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $aadhar = trim($_POST['aadhar']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    // Check if email or aadhar already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR aadhar = ?");
    $stmt->bind_param("ss", $email, $aadhar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email or Aadhar already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, aadhar, password, role) VALUES (?, ?, ?, ?, ?, 'student')");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $aadhar, $password);
        if ($stmt->execute()) {
            $success = "Voter added successfully!";
        } else {
            $error = "Error adding voter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Voter</title>
  <style>
    body {
      font-family: Poppins, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      background-color: #007bff;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      display: block;
      margin: 0 auto;
    }

    button:hover {
      background-color: #0056b3;
    }

    .message {
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
      color: green;
    }

    .error {
      color: red;
    }

    .back {
      text-align: center;
      margin-top: 20px;
    }

    .back a {
      text-decoration: none;
      color: #007bff;
    }

    .back a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Add New Voter</h2>

  <?php if ($success): ?>
    <p class="message"><?= htmlspecialchars($success) ?></p>
  <?php elseif ($error): ?>
    <p class="message error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form action="" method="POST">
    <label for="firstname">First Name</label>
    <input type="text" name="firstname" id="firstname" required>

    <label for="lastname">Last Name</label>
    <input type="text" name="lastname" id="lastname" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="aadhar">Aadhar Number</label>
    <input type="text" name="aadhar" id="aadhar" pattern="\d{12}" title="Enter 12-digit Aadhar" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Add Voter</button>
  </form>

  <div class="back">
    <a href="voters.php">⬅ Back to Voter List</a>
  </div>
</div>

</body>
</html>
