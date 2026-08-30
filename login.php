<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$msg_type = '';

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    session_start();
    $message = 'You have been logged out successfully.';
    $msg_type = 'info';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    require_once __DIR__ . '/config/database.php';
    $pdo = getDBConnection();

    if ($pdo && $username !== '') {
        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                header("Location: dashboard.php");
                exit;
            }

            $message = 'Invalid username or password.';
            $msg_type = 'danger';
        } catch (Exception $e) {
            $message = 'Unable to connect to the database. Please contact the administrator.';
            $msg_type = 'danger';
        }
    } else {
        if ($username === 'admin' && $password === 'password123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_role'] = 'Admin';
            $_SESSION['user_name'] = 'Dr. Sarah Jenkins';
            $_SESSION['user_email'] = 'admin@pharma.com';
            header("Location: dashboard.php");
            exit;
        }

        if ($username === 'pharmacist' && $password === 'password123') {
            $_SESSION['user_id'] = 2;
            $_SESSION['user_role'] = 'Pharmacist';
            $_SESSION['user_name'] = 'Alex Rivera, PharmD';
            $_SESSION['user_email'] = 'alex@pharma.com';
            header("Location: dashboard.php");
            exit;
        }

        if ($username === 'staff1' && $password === 'password123') {
            $_SESSION['user_id'] = 3;
            $_SESSION['user_role'] = 'Staff';
            $_SESSION['user_name'] = 'David Miller';
            $_SESSION['user_email'] = 'david@pharma.com';
            header("Location: dashboard.php");
            exit;
        }

        $message = 'Invalid username or password.';
        $msg_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mergen Pharmacy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #0d9488 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .login-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2.5rem;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 16px rgba(13, 148, 136, 0.3);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <div class="login-logo-icon">
            <i class="fa-solid fa-prescription-bottle-medical"></i>
        </div>
        <h2>Mergen Pharmacy</h2>
        <p style="font-size: 0.85rem; color: #64748b;">Healthcare Management System</p>
    </div>

    <?php if (!empty($message)): ?>
        <div style="padding: 0.75rem; background: #e0f2fe; color: #0369a1; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; text-align: center;">
            <i class="fa-solid fa-circle-info"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label class="form-label"><i class="fa-regular fa-user"></i> Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username (e.g. admin)" value="admin" required>
        </div>

        <div class="form-group">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
                <a href="forgot_password.php" style="font-size:0.78rem;">Forgot Password?</a>
            </div>
            <input type="password" name="password" class="form-control" placeholder="••••••••" value="password123" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%; margin-top:0.5rem;">
            <i class="fa-solid fa-right-to-bracket"></i> Login to System
        </button>
    </form>


</div>

</body>
</html>
