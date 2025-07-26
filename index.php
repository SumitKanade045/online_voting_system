<?php
session_start();
include "config.php";

// Get the election title from election_status table
$title_result = $conn->query("SELECT title FROM election_status WHERE id = 1");
$title_row = $title_result->fetch_assoc();
$election_title = $title_row ? $title_row['title'] : "Online Voting";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($election_title); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    body {
      background: #f0f2f5;
      color: #333;
    }

    header {
      background: #007bff;
      color: white;
      padding: 20px 0;
      text-align: center;
    }

    header h1 {
      font-size: 2rem;
    }

    .hero {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      text-align: center;
    }

    .hero h2 {
      font-size: 2rem;
      margin-bottom: 10px;
    }

    .hero p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 30px;
      max-width: 600px;
    }

    .buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .buttons a {
      text-decoration: none;
      padding: 12px 24px;
      background-color: #007bff;
      color: white;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .buttons a:hover {
      background-color: #0056b3;
    }

    .election-title {
      margin-top: 40px;
      font-size: 1.2rem;
      color: #222;
      font-weight: bold;
    }

    footer {
      margin-top: 60px;
      padding: 15px;
      text-align: center;
      background-color: #f1f1f1;
      font-size: 0.9rem;
      color: #666;
    }

    @media (max-width: 500px) {
      .buttons {
        flex-direction: column;
        width: 100%;
        align-items: center;
      }

      .buttons a {
        width: 80%;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>College Online Voting System</h1>
  </header>

  <section class="hero">
    <h2>Welcome to the Secure Student Election Portal</h2>
    <p>Vote for your favorite candidates easily and securely online. Your vote matters — make it count!</p>
    <div class="election-title">🗳️ Current Election: <span><?php echo htmlspecialchars($election_title); ?></span></div>
    <div class="buttons" style="margin-top: 30px;">
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
      <a href="results.php">View Results</a>
    </div>
  </section>

  <footer>
    &copy; 2025 College Voting System | All rights reserved.
  </footer>

</body>
</html>
