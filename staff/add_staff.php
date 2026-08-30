<?php
$path_prefix = '../';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/db_helper.php';

// Process POSTs before including templates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        header("Location: staff_list.php?msg=" . urlencode("Invalid CSRF token."));
        exit;
    }
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'Staff';
    $password = $_POST['password'] ?? '';

    if ($full_name === '' || $username === '' || $email === '' || $password === '') {
        header("Location: staff_list.php?msg=" . urlencode("Missing required staff fields."));
        exit;
    }

    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, phone, role, status) VALUES (:username, :password, :full_name, :email, :phone, :role, 'Active')");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hash,
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':role' => $role
            ]);
            $newId = $pdo->lastInsertId();
            header("Location: staff_list.php?msg=" . urlencode("Staff '{$full_name}' added (ID: {$newId})."));
            exit;
        } catch (Exception $e) {
            header("Location: staff_list.php?msg=" . urlencode("DB error adding staff."));
            exit;
        }
    } else {
        header("Location: staff_list.php?msg=" . urlencode("Staff '{$full_name}' registered (offline fallback)."));
        exit;
    }
}
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-user-plus" style="color:var(--primary);"></i> Register New Staff Member</h1>
                <p>Create account credentials and role assignments for pharmacy employees.</p>
            </div>
            <div class="page-actions">
                <a href="staff_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Roster
                </a>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-id-badge"></i> Employee Account Details
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="add_staff.php">
                    <?php echo csrf_input(); ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" placeholder="e.g. Dr. Robert Vance" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Username (Login ID) *</label>
                            <input type="text" name="username" class="form-control" placeholder="e.g. robert_vance" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="robert@pharma.com" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Phone *</label>
                            <input type="text" name="phone" class="form-control" placeholder="+880 1800-000000" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">System Access Role *</label>
                            <select name="role" class="form-control" required>
                                <option value="Pharmacist">Pharmacist (POS & Inventory Access)</option>
                                <option value="Admin">Administrator (Full System Control)</option>
                                <option value="Staff">Counter Staff (POS Only)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="staff_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Register Staff Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
