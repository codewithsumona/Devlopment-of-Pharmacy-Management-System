<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $message = "Password reset instructions have been sent to " . htmlspecialchars($email) . " (Demo Simulation).";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Mergen Pharmacy</title>
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
        .forgot-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2.5rem;
        }
    </style>
</head>
<body>

<div class="forgot-card">
    <div style="text-align: center; margin-bottom: 1.5rem;">
        <div style="width: 50px; height: 50px; background: #ccfbf1; border-radius: 50%; color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2>Reset Password</h2>
        <p style="font-size: 0.85rem; color: #64748b; margin-top:0.2rem;">Enter registered email address to receive password recovery link.</p>
    </div>

    <?php if (!empty($message)): ?>
        <div style="padding: 0.85rem; background: #d1fae5; color: #047857; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1.25rem; text-align: center;">
            <i class="fa-solid fa-circle-check"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="forgot_password.php">
        <div class="form-group">
            <label class="form-label"><i class="fa-regular fa-envelope"></i> Registered Email</label>
            <input type="email" name="email" class="form-control" placeholder="admin@pharma.com" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%; margin-top:0.5rem;">
            <i class="fa-solid fa-paper-plane"></i> Send Reset Link
        </button>
    </form>

    <div style="text-align:center; margin-top:1.5rem;">
        <a href="login.php" style="font-size:0.85rem; color:var(--text-secondary); text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Back to Login
        </a>
    </div>
</div>

</body>
</html>
