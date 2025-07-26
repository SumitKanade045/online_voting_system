<?php
include "config.php";

$sql = "SELECT u.firstname AS voter_firstname, u.lastname AS voter_lastname, 
               c.firstname AS candidate_firstname, c.lastname AS candidate_lastname, 
               p.name AS position
        FROM votes v
        JOIN users u ON v.aadhar = u.aadhar
        JOIN candidates c ON v.candidate_id = c.id
        JOIN positions p ON c.position_id = p.id
        ORDER BY p.name, u.firstname";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Votes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #f4f4f4;
    }

    h2 {
      color: #333;
    }

    .back-button {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .back-button:hover {
      background-color: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }

    thead {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>

  <!-- Back Button -->
  <a href="admin-dashboard.php" class="back-button">⬅️ Back to Admin Dashboard</a>

  <!-- Vote Records -->
  <h2>Vote Records</h2>
  <table>
    <thead>
      <tr>
        <th>Voter Name</th>
        <th>Position</th>
        <th>Candidate Voted</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['voter_firstname'] . ' ' . $row['voter_lastname']) ?></td>
          <td><?= htmlspecialchars($row['position']) ?></td>
          <td><?= htmlspecialchars($row['candidate_firstname'] . ' ' . $row['candidate_lastname']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
