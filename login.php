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

// Handle Form Submission or 1-Click Demo Logins
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_type = $_POST['demo_login'] ?? '';
    
    if ($role_type === 'admin' || ($_POST['username'] ?? '') === 'admin') {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'Admin';
        $_SESSION['user_name'] = 'Dr. Sarah Jenkins';
        $_SESSION['user_email'] = 'admin@pharma.com';
        header("Location: dashboard.php");
        exit;
    } else if ($role_type === 'staff' || ($_POST['username'] ?? '') === 'pharmacist' || ($_POST['username'] ?? '') === 'staff') {
        $_SESSION['user_id'] = 2;
        $_SESSION['user_role'] = 'Pharmacist';
        $_SESSION['user_name'] = 'Alex Rivera, PharmD';
        $_SESSION['user_email'] = 'alex@pharma.com';
        header("Location: dashboard.php");
        exit;
    } else {
        // Fallback default redirect for prototype presentation
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'Admin';
        $_SESSION['user_name'] = 'Dr. Sarah Jenkins';
        $_SESSION['user_email'] = 'admin@pharma.com';
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PharmaCare Pro Prototype</title>
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
        .demo-presets {
            border-top: 1px solid var(--border-color);
            margin-top: 1.5rem;
            padding-top: 1.5rem;
        }
        .preset-btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <div class="login-logo-icon">
            <i class="fa-solid fa-prescription-bottle-medical"></i>
        </div>
        <h2>PharmaCare PRO</h2>
        <p style="font-size: 0.85rem; color: #64748b;">University System Analysis & Design Prototype</p>
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

    <!-- Lab Presentation Quick 1-Click Fill Section -->
    <div class="demo-presets">
        <p style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; text-align: center; letter-spacing: 0.5px;">
            ⚡ Quick Demo Logins (Presentation Mode)
        </p>
        <form method="POST" action="login.php" class="preset-btn-grid">
            <button type="submit" name="demo_login" value="admin" class="btn btn-outline btn-sm" style="border-color:#10b981; color:#047857; background:#ecfdf5;">
                <i class="fa-solid fa-user-shield"></i> Admin Role
            </button>
            <button type="submit" name="demo_login" value="staff" class="btn btn-outline btn-sm" style="border-color:#0ea5e9; color:#0369a1; background:#f0f9ff;">
                <i class="fa-solid fa-user-doctor"></i> Staff / Pharmacist
            </button>
        </form>
    </div>
</div>

</body>
</html>
