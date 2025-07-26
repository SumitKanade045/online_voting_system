<?php
session_start();
include "config.php";

if (!isset($_SESSION['aadhar']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Update status to paused
$status = 'paused';
$sql = "UPDATE election_status SET status = ? WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $status);

if ($stmt->execute()) {
    $_SESSION['message'] = "Election paused successfully.";
} else {
    $_SESSION['error'] = "Failed to pause election.";
}

header("Location: admin-dashboard.php");
exit();
?>
