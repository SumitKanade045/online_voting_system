<?php
session_start();
include "config.php";

// Only allow admin
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch all positions
$positions = $conn->query("SELECT * FROM positions");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_id = $_POST['position'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $platform = trim($_POST['platform']);

    // Handle image upload
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true); // Create directory if it doesn't exist
    }

    $photo_name = basename($_FILES["photo"]["name"]);
    $extension = pathinfo($photo_name, PATHINFO_EXTENSION);
    $unique_name = time() . "_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $photo_name);
    $target_file = $upload_dir . $unique_name;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO candidates (position_id, firstname, lastname, photo, platform) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $position_id, $firstname, $lastname, $target_file, $platform);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Candidate added successfully!'); window.location.href = 'candidates.php';</script>";
        } else {
            echo "<script>alert('❌ Database error. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('❌ Failed to upload photo. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Candidate</title>
  <style>
    body {
      font-family: Poppins, sans-serif;
      background: #f4f4f4;
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
      margin-bottom: 8px;
      font-weight: 500;
    }

    input[type="text"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    input[type="file"] {
      margin-bottom: 20px;
    }

    button {
      background-color: #007bff;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
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
  <h2>Add New Candidate</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <label for="position">Position</label>
    <select name="position" id="position" required>
      <option value="">-- Select Position --</option>
      <?php while ($row = $positions->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="firstname">First Name</label>
    <input type="text" name="firstname" id="firstname" required>

    <label for="lastname">Last Name</label>
    <input type="text" name="lastname" id="lastname" required>

    <label for="platform">Platform Statement</label>
    <textarea name="platform" id="platform" rows="4" required></textarea>

    <label for="photo">Photo</label>
    <input type="file" name="photo" id="photo" accept="image/*" required>

    <button type="submit">Add Candidate</button>
  </form>

  <div class="back">
    <a href="candidates.php">⬅ Back to Candidate List</a>
  </div>
</div>

</body>
</html>
