<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$role = $_SESSION['user_role'] ?? 'Admin';
$name = $_SESSION['user_name'] ?? 'Dr. Sarah Jenkins';
$email = $_SESSION['user_email'] ?? 'admin@pharma.com';
$phone = ($role === 'Admin') ? '+880 1711-000111' : '+880 1819-222333';
$username = strtolower(explode(' ', $name)[0]);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $_SESSION['user_name'] = htmlspecialchars($_POST['full_name']);
        $_SESSION['user_email'] = htmlspecialchars($_POST['email']);
        $name = $_SESSION['user_name'];
        $email = $_SESSION['user_email'];
        $message = "Profile details updated successfully!";
    } elseif (isset($_POST['change_password'])) {
        $message = "Password updated successfully!";
    }
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-user-gear" style="color:var(--primary);"></i> User Profile & Account Settings</h1>
                <p>Manage your account credentials, view role permissions, and update contact information.</p>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div style="padding:0.75rem 1.25rem; background:#d1fae5; color:#047857; border-radius:8px; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem;">
            <!-- Profile Identity Card -->
            <div class="card">
                <div class="card-body" style="text-align:center; padding:2rem 1.5rem;">
                    <div style="width:90px; height:90px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--secondary)); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:2.5rem; font-weight:bold; margin-bottom:1rem; box-shadow:0 8px 16px rgba(13, 148, 136, 0.3);">
                        <?php echo strtoupper(substr($name, 0, 1)); ?>
                    </div>
                    <h2 style="font-size:1.3rem; margin-bottom:0.2rem;"><?php echo htmlspecialchars($name); ?></h2>
                    <p style="color:#64748b; font-size:0.85rem; margin-bottom:0.8rem;"><?php echo htmlspecialchars($email); ?></p>
                    
                    <span class="badge" style="background:#ccfbf1; color:#0f766e; font-size:0.85rem; padding:0.4rem 1rem;">
                        <i class="fa-solid fa-shield-halved"></i> <?php echo htmlspecialchars($role); ?> Access
                    </span>

                    <hr style="margin:1.5rem 0; border:none; border-top:1px solid var(--border-color);">

                    <div style="text-align:left; display:flex; flex-direction:column; gap:0.75rem; font-size:0.9rem;">
                        <div><strong style="color:#64748b;">Username:</strong> <code><?php echo htmlspecialchars($username); ?></code></div>
                        <div><strong style="color:#64748b;">Phone:</strong> <?php echo htmlspecialchars($phone); ?></div>
                        <div><strong style="color:#64748b;">Department:</strong> Pharmacy Operations</div>
                        <div><strong style="color:#64748b;">Account Status:</strong> <span class="badge badge-in-stock">Active</span></div>
                    </div>

                    <a href="../login.php?logout=1" class="btn btn-outline" style="width:100%; margin-top:1.5rem; color:var(--danger); border-color:#fca5a5;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout of System
                    </a>
                </div>
            </div>

            <!-- Profile Edit & Password Change Forms -->
            <div>
                <!-- Edit Profile Form -->
                <div class="card" style="margin-bottom:1.5rem;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-user-pen"></i> Edit Personal Profile
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">System Role (Assigned)</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($role); ?>" disabled>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin-top:1rem;">
                                <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Security / Change Password Form -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-lock"></i> Change Security Password
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="change_password" value="1">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" placeholder="••••••••" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary" style="margin-top:1rem;">
                                <i class="fa-solid fa-key"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
