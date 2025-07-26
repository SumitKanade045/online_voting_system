<?php
session_start();
include "config.php";

// Admin only
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied'); window.location.href = 'login.html';</script>";
    exit();
}

// Fetch all voters
$sql = "SELECT * FROM users WHERE role = 'student' ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Voters List</title>
  <style>
    body {
      font-family: Poppins, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    .top-actions {
      text-align: right;
      margin-bottom: 20px;
    }

    .top-actions a {
      padding: 10px 15px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-left: 10px;
    }

    .top-actions a:hover {
      background-color: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #f2f2f2;
    }

    .delete-btn {
      background: #dc3545;
      color: white;
      padding: 6px 10px;
      text-decoration: none;
      border-radius: 5px;
    }

    .delete-btn:hover {
      background: #c82333;
    }

    .back {
      text-align: center;
      margin-top: 25px;
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
  <h2>Registered Voters</h2>

  <div class="top-actions">
    <a href="add_voter.php">+ Add Voter</a>
    <a href="admin-dashboard.php" style="background-color: gray;">⬅ Back</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Aadhar</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['aadhar']) ?></td>
            <td>
              <a class="delete-btn" href="delete_voter.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this voter?');">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">No voters found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="back">
    <a href="admin-dashboard.php">⬅ Back to Dashboard</a>
  </div>
</div>

</body>
</html>
