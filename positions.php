<?php
session_start();
include "config.php";

// Admin access only
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.html';</script>";
    exit();
}

// Add new position
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['position_name'])) {
    $position_name = trim($_POST['position_name']);

    if (!empty($position_name)) {
        $stmt = $conn->prepare("INSERT INTO positions (name) VALUES (?)");
        $stmt->bind_param("s", $position_name);
        $stmt->execute();
        echo "<script>alert('Position added successfully'); window.location.href='positions.php';</script>";
    } else {
        echo "<script>alert('Position name cannot be empty');</script>";
    }
}

// Delete position
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM positions WHERE id = $id");
    echo "<script>alert('Position deleted successfully'); window.location.href='positions.php';</script>";
}

// Fetch all positions
$positions = $conn->query("SELECT * FROM positions ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Positions</title>
  <style>
    body {
      font-family: Poppins, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
    }

    .container {
      max-width: 700px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    form {
      margin-bottom: 30px;
      text-align: center;
    }

    input[type="text"] {
      width: 60%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    button {
      padding: 10px 20px;
      margin-left: 10px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 15px;
    }

    button:hover {
      background-color: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      text-align: center;
      padding: 12px;
      border: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }

    .delete-btn {
      background: #dc3545;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
    }

    .delete-btn:hover {
      background: #c82333;
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
  <h2>Manage Positions</h2>

  <form action="" method="POST">
    <input type="text" name="position_name" placeholder="Enter position name (e.g. CR, President)" required>
    <button type="submit">Add Position</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Position Name</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($positions->num_rows > 0): ?>
        <?php while ($row = $positions->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>
              <a href="positions.php?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this position?');">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3">No positions found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="back">
    <a href="admin-dashboard.php">⬅ Back to Dashboard</a>
  </div>
</div>

</body>
</html>
