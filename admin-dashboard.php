<?php
session_start();
include "config.php";

// Only allow admin
if (!isset($_SESSION['aadhar']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle voting status actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $allowed = ['not_started', 'started', 'ended'];

    if (in_array($action, $allowed)) {
        $stmt = $conn->prepare("UPDATE election_status SET status = ? WHERE id = 1");
        $stmt->bind_param("s", $action);
        $stmt->execute();

        if ($action === 'not_started') {
            $conn->query("DELETE FROM votes");
        }

        header("Location: admin-dashboard.php");
        exit();
    }
}

// Handle result publishing
if (isset($_GET['publish'])) {
    $publish = ($_GET['publish'] === 'true') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE election_status SET results_published = ? WHERE id = 1");
    $stmt->bind_param("i", $publish);
    $stmt->execute();

    header("Location: admin-dashboard.php");
    exit();
}

// Handle registration control
if (isset($_GET['register'])) {
    $register = ($_GET['register'] === 'true') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE election_status SET registration_open = ? WHERE id = 1");
    $stmt->bind_param("i", $register);
    $stmt->execute();

    header("Location: admin-dashboard.php");
    exit();
}

// Handle election title update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['election_title'])) {
    $newTitle = trim($_POST['election_title']);
    $stmt = $conn->prepare("UPDATE election_status SET title = ? WHERE id = 1");
    $stmt->bind_param("s", $newTitle);
    $stmt->execute();

    header("Location: admin-dashboard.php");
    exit();
}

// Fetch status
$status_result = $conn->query("SELECT status, results_published, title, registration_open FROM election_status WHERE id = 1");
$status_row = $status_result->fetch_assoc();
$current_status = strtoupper($status_row['status']);
$is_published = $status_row['results_published'];
$current_title = htmlspecialchars($status_row['title']);
$registration_open = $status_row['registration_open'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Online Voting</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f4f4;
    }

    header {
      background-color: #007bff;
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }

    .logout-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #dc3545;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .logout-btn:hover {
      background: #c82333;
    }

    nav {
      background: #333;
      padding: 10px;
    }

    nav a {
      color: white;
      margin-right: 15px;
      text-decoration: none;
      font-weight: bold;
    }

    .container {
      padding: 20px;
    }

    h2 {
      color: #333;
    }

    .status-box {
      padding: 15px;
      background: #e9ecef;
      margin-bottom: 20px;
      border-radius: 6px;
    }

    .controls button {
      margin: 8px 10px 8px 0;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: white;
      font-size: 14px;
    }

    .start { background: #28a745; }
    .end { background: #dc3545; }
    .reset { background: #6c757d; }
    .publish { background: #17a2b8; }
    .unpublish { background: #ff5733; }
    .open-reg { background: #007bff; }
    .close-reg { background: #ff8800; }

    .title-form {
      margin-top: 30px;
      padding: 20px;
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .title-form input[type="text"] {
      width: 70%;
      padding: 10px;
      font-size: 16px;
      margin-right: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .title-form button {
      padding: 10px 20px;
      font-size: 16px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
    }

    .title-form button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

<header>
  <h1>Admin Dashboard - Online Voting</h1>
  <form method="post" action="logout.php" style="display:inline;">
    <button type="submit" class="logout-btn">Logout</button>
  </form>
</header>

<nav>
  <!-- No longer linking to admin-dashboard.php inside itself -->
  <a href="#">Dashboard</a>
  <a href="positions.php" target="_self">Manage Positions</a>
  <a href="candidates.php" target="_self">Manage Candidates</a>
  <a href="voters.php" target="_self">View Voters</a>
  <a href="view_votes.php" target="_self">View Votes</a>
</nav>

<div class="container">
  <h2>Election Control</h2>

  <div class="status-box">
    <p><strong>Election Title:</strong> <?= $current_title ?></p>
    <p><strong>Current Voting Status:</strong> <?= $current_status ?></p>
    <p><strong>Results Published:</strong> <?= $is_published ? "✅ Yes" : "❌ No" ?></p>
    <p><strong>Registration:</strong> <?= $registration_open ? "🟢 Open" : "🔴 Closed" ?></p>
  </div>

  <div class="controls">
    <a href="?action=started"><button class="start">Start Voting</button></a>
    <a href="?action=ended"><button class="end">End Voting</button></a>
    <a href="?action=not_started" onclick="return confirm('⚠️ Are you sure? This will delete all votes!');">
      <button class="reset">Reset Voting</button>
    </a>

    <?php if (!$is_published): ?>
      <a href="?publish=true"><button class="publish">✅ Publish Results</button></a>
    <?php else: ?>
      <a href="?publish=false"><button class="unpublish">❌ Unpublish Results</button></a>
    <?php endif; ?>

    <?php if (!$registration_open): ?>
      <a href="?register=true"><button class="open-reg">🟢 Start Registration</button></a>
    <?php else: ?>
      <a href="?register=false"><button class="close-reg">🛑 Stop Registration</button></a>
    <?php endif; ?>
  </div>

  <div class="title-form">
    <h3>Change Election Title</h3>
    <form method="post" action="">
      <input type="text" name="election_title" value="<?= $current_title ?>" required>
      <button type="submit">Update Title</button>
    </form>
  </div>
</div>

</body>
</html>
