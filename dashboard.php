<?php
session_start();
include "config.php";

// Check login
if (!isset($_SESSION["aadhar"])) {
    header("Location: login.php");
    exit();
}

$aadhar = $_SESSION["aadhar"];

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE aadhar = ?");
$stmt->bind_param("s", $aadhar);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Check election status
$status_result = $conn->query("SELECT status FROM election_status WHERE id = 1");
$current_status = $status_result->fetch_assoc()['status'];

if ($current_status === 'not_started') {
    echo "
    <html><head><title>Voting Not Started</title>
    <style>
      body { font-family: Arial; background: #f4f4f4; text-align: center; padding: 50px; }
      .message { background: #fff; padding: 30px; border-radius: 8px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
      a { text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
    </head><body>
      <div class='message'>
        <h2>🕒 Voting has not started yet.</h2>
        <p>Please check back later.</p>
        <a href='logout.php'>Logout</a>
      </div>
    </body></html>";
    exit();
} elseif ($current_status === 'ended') {
    echo "<script>alert('Voting has ended. Thank you for participating!'); window.location.href = 'logout.php';</script>";
    exit();
}

// Fetch all positions
$positions_result = $conn->query("SELECT * FROM positions");
$positions = [];
while ($pos = $positions_result->fetch_assoc()) {
    $positions[] = $pos;
}

// Check for each position whether student has voted
$votes_query = $conn->prepare("SELECT position_id FROM votes WHERE aadhar = ?");
$votes_query->bind_param("s", $aadhar);
$votes_query->execute();
$votes_result = $votes_query->get_result();
$already_voted_positions = [];
while ($row = $votes_result->fetch_assoc()) {
    $already_voted_positions[] = $row['position_id'];
}

// Fetch candidates grouped by position
$sql = "SELECT c.id AS candidate_id, c.firstname, c.lastname, c.photo, c.platform, p.id AS position_id, p.name AS position 
        FROM candidates c
        JOIN positions p ON c.position_id = p.id
        ORDER BY p.name";
$candidates = $conn->query($sql);

$grouped = [];
while ($row = $candidates->fetch_assoc()) {
    $grouped[$row['position_id']]['name'] = $row['position'];
    $grouped[$row['position_id']]['candidates'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Voter Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { margin: 0; background-color: #f4f4f4; }
    header {
      background-color: #007bff;
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }
    .logout-btn {
      position: absolute;
      right: 20px;
      top: 20px;
      background-color: #dc3545;
      border: none;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
    }
    .container {
      max-width: 1000px;
      margin: 20px auto;
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 30px; }
    .position-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-top: 30px;
      border-bottom: 2px solid #ddd;
      padding-bottom: 8px;
      color: #333;
    }
    .candidates {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 15px;
    }
    .card {
      width: 240px;
      background: #f8f8f8;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .card img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }
    .card h4 { margin: 5px 0; }
    .card p { font-size: 0.9rem; color: #555; margin-bottom: 10px; }
    .card input[type="radio"] { transform: scale(1.2); }
    .submit-btn { text-align: center; margin-top: 30px; }
    .submit-btn button {
      background-color: #28a745;
      color: white;
      padding: 12px 30px;
      border: none;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }
    .submit-btn button:hover { background-color: #218838; }
    .voted-msg {
      text-align: center;
      font-size: 1.1rem;
      color: #dc3545;
      font-weight: bold;
    }
  </style>
</head>
<body>

<header>
  <h1>Welcome, <?= htmlspecialchars($user['firstname']) ?>!</h1>
  <form method="post" action="logout.php" style="position:absolute; right: 20px; top: 20px;">
    <button type="submit" class="logout-btn">Logout</button>
  </form>
</header>

<div class="container">
  <h2>Cast Your Vote</h2>

  <form action="submit_vote.php" method="POST">
    <?php
    $has_positions_to_vote = false;
    foreach ($grouped as $position_id => $data):
      if (in_array($position_id, $already_voted_positions)) continue;
      $has_positions_to_vote = true;
    ?>
      <div class="position-title"> <?= htmlspecialchars($data['name']) ?> </div>
      <div class="candidates">
        <?php foreach ($data['candidates'] as $index => $cand): ?>
          <div class="card">
            <img src="<?= htmlspecialchars($cand['photo']) ?>" alt="Candidate Photo">
            <h4><?= htmlspecialchars($cand['firstname'] . " " . $cand['lastname']) ?></h4>
            <p><?= htmlspecialchars($cand['platform']) ?></p>
            <input type="radio" name="vote[<?= $position_id ?>]" value="<?= $cand['candidate_id'] ?>" <?= $index === 0 ? 'checked' : '' ?> required>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <?php if ($has_positions_to_vote): ?>
      <div class="submit-btn">
        <button type="submit">Submit Vote</button>
      </div>
    <?php else: ?>
      <p class="voted-msg">You have voted for all available positions. Thank you!</p>
    <?php endif; ?>
  </form>
</div>

</body>
</html>
