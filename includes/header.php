<?php
// Start output buffering as early as possible to prevent "headers already sent" issues
if (!ob_get_level()) ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_helper.php';
$current_role = $_SESSION['user_role'] ?? 'Admin';
require_once __DIR__ . '/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mergen Pharmacy - Management System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Core Application CSS -->
    <link rel="stylesheet" href="<?php echo $path_prefix ?? ''; ?>css/style.css">
</head>
<?php
?>
<body>
<div class="app-container">
<script>window.CSRF_TOKEN = '<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, "UTF-8"); ?>';</script>
