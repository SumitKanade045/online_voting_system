<?php
ob_start();
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aadhar = $_POST["aadhar"] ?? '';
    $password = $_POST["password"] ?? '';
    $role = strtolower(trim($_POST["role"] ?? ''));

    if (empty($aadhar) || empty($password) || empty($role)) {
        $_SESSION['error'] = "❌ All fields are required.";
        header("Location: login.php");
        exit();
    }

    if (!preg_match('/^\d{12}$/', $aadhar)) {
        $_SESSION['error'] = "❌ Aadhar must be exactly 12 digits.";
        header("Location: login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE aadhar = ? AND role = ?");
    $stmt->bind_param("ss", $aadhar, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION["aadhar"] = $user["aadhar"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["firstname"] = $user["firstname"];

            if ($user["role"] === "admin") {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "❌ Incorrect password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "❌ No user found with provided Aadhar and role.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "❌ Invalid request method.";
    header("Location: login.php");
    exit();
}
?>
