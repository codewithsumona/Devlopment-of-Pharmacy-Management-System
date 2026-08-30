<?php
$path_prefix = '../';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/db_helper.php';

// Handle POST before templates
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
    $status = $_POST['status'] ?? 'Active';

    if ($full_name === '' || $username === '' || $email === '') {
        header("Location: staff_list.php?msg=" . urlencode("Missing required staff fields."));
        exit;
    }

    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = :username, full_name = :full_name, email = :email, phone = :phone, role = :role, status = :status WHERE id = :id");
            $stmt->execute([
                ':username' => $username,
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':role' => $role,
                ':status' => $status,
                ':id' => $target_staff['id']
            ]);
            header("Location: staff_list.php?msg=" . urlencode("Staff '{$full_name}' updated."));
            exit;
        } catch (Exception $e) {
            header("Location: staff_list.php?msg=" . urlencode("DB error updating staff."));
            exit;
        }
    } else {
        header("Location: staff_list.php?msg=" . urlencode("Staff '{$full_name}' updated (offline fallback)."));
        exit;
    }
}
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$staff_id = $_GET['id'] ?? 1;
$staff_members = get_all_staff();
$target_staff = null;

foreach ($staff_members as $st) {
    if ($st['id'] == $staff_id) {
        $target_staff = $st;
        break;
    }
}
if (!$target_staff) $target_staff = $staff_members[0];
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-user-pen" style="color:var(--secondary);"></i> Edit Staff Profile</h1>
                <p>Update access permissions and profile details for <strong><?php echo htmlspecialchars($target_staff['full_name']); ?></strong></p>
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
                    <i class="fa-solid fa-id-badge"></i> Edit Profile (EMP-00<?php echo $target_staff['id']; ?>)
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="edit_staff.php?id=<?php echo $target_staff['id']; ?>">
                    <?php echo csrf_input(); ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($target_staff['full_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Username (Login ID) *</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($target_staff['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($target_staff['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Phone *</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($target_staff['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">System Access Role *</label>
                            <select name="role" class="form-control" required>
                                <option value="Admin" <?php echo ($target_staff['role'] == 'Admin') ? 'selected' : ''; ?>>Administrator (Full System Control)</option>
                                <option value="Pharmacist" <?php echo ($target_staff['role'] == 'Pharmacist') ? 'selected' : ''; ?>>Pharmacist (POS & Inventory Access)</option>
                                <option value="Staff" <?php echo ($target_staff['role'] == 'Staff') ? 'selected' : ''; ?>>Counter Staff (POS Only)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Account Status</label>
                            <select name="status" class="form-control">
                                <option value="Active" <?php echo ($target_staff['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo ($target_staff['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive / Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="staff_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Update Employee Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
