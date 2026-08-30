<?php
require_once __DIR__ . '/config/database.php';
$pdo = getDBConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DB Connection Test</title>
    <style>body{font-family:Inter,Arial;padding:2rem;background:#f8fafc;color:#0f172a}</style>
</head>
<body>
    <h2>Database Connection Test</h2>
    <p>
    <?php if ($pdo): ?>
        <strong style="color:green;">Success:</strong> Connected to database '<?php echo htmlspecialchars(DB_NAME); ?>'.
    <?php else: ?>
        <strong style="color:crimson;">Failure:</strong> Could not connect to database. Check <code>config/database.php</code> and <code>logs/db_errors.log</code> for details.
    <?php endif; ?>
    </p>
</body>
</html>