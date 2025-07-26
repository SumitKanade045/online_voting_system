<?php
session_start();
include "config.php";

// Only allow admin
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.html';</script>";
    exit();
}

// Delete all vote records
$sql = "DELETE FROM votes";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('All votes have been reset successfully.'); window.location.href = 'admin-dashboard.php';</script>";
} else {
    echo "<script>alert('Error resetting votes: " . $conn->error . "'); window.location.href = 'admin-dashboard.php';</script>";
}
?>
