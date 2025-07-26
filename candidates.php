<?php
session_start();
include "config.php";

if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.html';</script>";
    exit();
}

// Fetch candidates with position info
$sql = "SELECT c.id, c.firstname, c.lastname, c.platform, c.photo, p.name AS position 
        FROM candidates c 
        JOIN positions p ON c.position_id = p.id 
        ORDER BY p.name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Candidates - Admin</title>
  <style>
    body {
      font-family: Poppins, sans-serif;
      background: #f4f4f4;
      margin: 0;
    }

    header {
      background: #343a40;
      color: white;
      padding: 15px;
      text-align: center;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .top-actions {
      text-align: right;
      margin-bottom: 15px;
    }

    .top-actions a {
      padding: 10px 15px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .top-actions a:hover {
      background: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th, table td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }

    table th {
      background-color: #f2f2f2;
    }

    img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
    }

    .action-buttons a {
      margin: 0 5px;
      padding: 6px 10px;
      text-decoration: none;
      border-radius: 5px;
      font-size: 14px;
    }

    .edit-btn {
      background: #ffc107;
      color: #000;
    }

    .delete-btn {
      background: #dc3545;
      color: white;
    }

    .edit-btn:hover {
      background: #e0a800;
    }

    .delete-btn:hover {
      background: #c82333;
    }
  </style>
</head>
<body>

<header>
  <h1>Manage Candidates</h1>
</header>

<div class="container">
  <h2>Candidate List</h2>

  <div class="top-actions">
    <a href="add_candidate.php">+ Add Candidate</a>
    <a href="admin-dashboard.php" style="background:gray;">⬅ Back</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>Photo</th>
        <th>Name</th>
        <th>Position</th>
        <th>Platform</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><img src="<?= htmlspecialchars($row['photo']) ?>" alt="Candidate"></td>
            <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
            <td><?= htmlspecialchars($row['position']) ?></td>
            <td><?= htmlspecialchars($row['platform']) ?></td>
            <td class="action-buttons">
              <a href="edit_candidate.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
              <a href="delete_candidate.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">No candidates found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
