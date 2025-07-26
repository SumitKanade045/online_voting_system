<?php
session_start();
include "config.php";

// Only admin allowed
if (!isset($_SESSION["aadhar"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('Access denied.'); window.location.href = 'login.html';</script>";
    exit();
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Make sure we are not deleting an admin
    $check = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['role'] === 'admin') {
            echo "<script>alert('Cannot delete an admin account!'); window.location.href = 'voters.php';</script>";
            exit();
        }
    }

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Voter deleted successfully'); window.location.href = 'voters.php';</script>";
    } else {
        echo "<script>alert('Error deleting voter'); window.location.href = 'voters.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'voters.php';</script>";
}
?>
