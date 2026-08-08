<?php
/**
 * Main Entry Point
 * Pharmacy Management System Prototype
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id']) || isset($_SESSION['user_role'])) {
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>
