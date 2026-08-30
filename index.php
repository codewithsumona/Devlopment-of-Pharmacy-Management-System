<?php
/**
 * Main Entry Point
 * Mergen Pharmacy Management System
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
