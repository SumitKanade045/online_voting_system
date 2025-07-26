<?php
session_start();
include "config.php";

if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.html';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE election_status SET status = ? WHERE id = 1");
    $stmt->bind_param("s", $status);
    if ($stmt->execute()) {
        echo "<script>alert('Election status updated to $status'); window.location.href = 'admin-dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to update status'); window.location.href = 'admin-dashboard.php';</script>";
    }
}
?>
