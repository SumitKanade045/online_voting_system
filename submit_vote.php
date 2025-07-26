<?php
session_start();
include "config.php";

if (!isset($_SESSION["aadhar"])) {
    header("Location: login.html");
    exit();
}

$aadhar = $_SESSION["aadhar"];

// Check if the voter already voted
$check_vote = $conn->prepare("SELECT * FROM votes WHERE aadhar = ?");
$check_vote->bind_param("s", $aadhar);
$check_vote->execute();
$result = $check_vote->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('You have already voted.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $votes = $_POST['vote'];

    // Prepare insert statement
    $insert = $conn->prepare("INSERT INTO votes (aadhar, candidate_id, position_id) VALUES (?, ?, ?)");

    foreach ($votes as $position => $candidate_id) {
        // Get position_id from candidates table
        $stmt = $conn->prepare("SELECT position_id FROM candidates WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $pos_result = $stmt->get_result()->fetch_assoc();
        $position_id = $pos_result['position_id'];

        $insert->bind_param("sii", $aadhar, $candidate_id, $position_id);
        $insert->execute();
    }

    echo "<script>alert('Vote submitted successfully!'); window.location.href = 'dashboard.php';</script>";
} else {
    echo "<script>alert('Invalid vote submission.'); window.location.href = 'dashboard.php';</script>";
}
?>
