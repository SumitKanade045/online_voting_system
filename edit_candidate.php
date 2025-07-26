<?php
session_start();
include "config.php";

// Only admin
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.html';</script>";
    exit();
}

// Check if ID is set
if (!isset($_GET['id'])) {
    echo "<script>alert('No candidate selected.'); window.location.href = 'candidates.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Get candidate details
$stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$candidate = $stmt->get_result()->fetch_assoc();

if (!$candidate) {
    echo "<script>alert('Candidate not found.'); window.location.href = 'candidates.php';</script>";
    exit();
}

// Fetch all positions
$positions = $conn->query("SELECT * FROM positions");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_id = $_POST['position'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $platform = $_POST['platform'];
    $photo_path = $candidate['photo']; // default to old photo

    // Check if a new photo is uploaded
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        $photo_name = basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . time() . "_" . $photo_name;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            echo "<script>alert('Photo upload failed.');</script>";
        }
    }

    // Update candidate
    $stmt = $conn->prepare("UPDATE candidates SET position_id = ?, firstname = ?, lastname = ?, platform = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("issssi", $position_id, $firstname, $lastname, $platform, $photo_path, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Candidate updated successfully'); window.location.href = 'candidates.php';</script>";
    } else {
        echo "<script>alert('Error updating candidate');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Candidate</title>
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
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    input[type="file"] {
      margin-bottom: 20px;
    }

    img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
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
  <h2>Edit Candidate</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <label for="position">Position</label>
    <select name="position" id="position" required>
      <option value="">-- Select Position --</option>
      <?php while ($row = $positions->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>" <?= $row['id'] == $candidate['position_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label for="firstname">First Name</label>
    <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($candidate['firstname']) ?>" required>

    <label for="lastname">Last Name</label>
    <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($candidate['lastname']) ?>" required>

    <label for="platform">Platform Statement</label>
    <textarea name="platform" id="platform" rows="4" required><?= htmlspecialchars($candidate['platform']) ?></textarea>

    <label>Current Photo</label><br>
    <img src="<?= htmlspecialchars($candidate['photo']) ?>" alt="Current Photo"><br>

    <label for="photo">Upload New Photo (optional)</label>
    <input type="file" name="photo" id="photo" accept="image/*">

    <button type="submit">Update Candidate</button>
  </form>

  <div class="back">
    <a href="candidates.php">⬅ Back to Candidates</a>
  </div>
</div>

</body>
</html>
