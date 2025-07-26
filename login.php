<?php
ob_start();
session_start();
include "config.php";

$error = "";

// Handle POST form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aadhar = trim($_POST["aadhar"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $role = strtolower(trim($_POST["role"] ?? ''));

    // Validate input
    if (empty($aadhar) || empty($password) || empty($role)) {
        $error = "❌ All fields are required.";
    } else {
        // Fetch user
        $stmt = $conn->prepare("SELECT * FROM users WHERE aadhar = ? AND role = ?");
        $stmt->bind_param("ss", $aadhar, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check password
            if (password_verify($password, $user["password"])) {
                $_SESSION["aadhar"] = $user["aadhar"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["firstname"] = $user["firstname"];
                $_SESSION["lastname"] = $user["lastname"];

                // Redirect to appropriate dashboard
                if ($user["role"] === "admin") {
                    header("Location: admin-dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ No user found with provided Aadhar and role.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Online Voting System</title>
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

    .login-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-container h2 {
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

    .input-group input,
    .input-group select {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .login-button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-button:hover {
      background-color: #0056b3;
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
    }

    .register-link a {
      color: #007bff;
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    .error-message {
      background-color: #ffe6e6;
      border-left: 5px solid #ff4d4d;
      padding: 10px 15px;
      margin-bottom: 20px;
      color: #a94442;
      border-radius: 6px;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="input-group">
        <label for="aadhar">Aadhar Number</label>
        <input type="text" id="aadhar" name="aadhar" pattern="\d{12}" maxlength="12" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>

      <div class="input-group">
        <label for="role">Login As</label>
        <select id="role" name="role" required>
          <option value="">-- Select Role --</option>
          <option value="student">Student</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <button type="submit" class="login-button">Login</button>
    </form>

    <div class="register-link">
      Don't have an account? <a href="register.php">Register here</a>
    </div>
  </div>

</body>
</html>
