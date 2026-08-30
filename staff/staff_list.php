<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$staff_members = get_all_staff();

// Handle delete action via POST + CSRF
$action_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    require_once __DIR__ . '/../includes/csrf.php';
    $delId = (int)$_POST['delete_id'];
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $action_msg = 'Invalid CSRF token.';
    } else {
        $pdo = getDBConnection();
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $delId]);
                $action_msg = "Staff member #{$delId} deleted successfully.";
            } catch (Exception $e) {
                $action_msg = "Unable to delete staff via DB (prototype fallback).";
            }
        } else {
            $action_msg = "Delete simulated (offline fallback) for Staff #{$delId}.";
        }
    }
    header('Location: staff_list.php?msg=' . urlencode($action_msg));
    exit;
}

if (isset($_GET['msg'])) {
    $action_msg = htmlspecialchars($_GET['msg']);
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-users-gear" style="color:var(--primary);"></i> Staff & Employee Management</h1>
                <p>Manage pharmacy personnel accounts, roles, access permissions, and status.</p>
            </div>
            <div class="page-actions">
                <a href="add_staff.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Add New Staff
                </a>
            </div>
        </div>

        <?php if (!empty($action_msg)): ?>
            <div style="padding:0.75rem 1.25rem; background:#d1fae5; color:#047857; border-radius:8px; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo $action_msg; ?></span>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-id-card"></i> Pharmacy Staff Roster
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <input type="text" id="staffSearchInput" class="form-control" style="width:240px; padding:0.4rem 0.8rem;" placeholder="Search employee..." onkeyup="filterTable('staffSearchInput', 'staffTable')">
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table" id="staffTable">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email Address</th>
                                <th>Phone</th>
                                <th>System Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff_members as $st): ?>
                            <tr>
                                <td><code>EMP-00<?php echo $st['id']; ?></code></td>
                                <td><strong><?php echo htmlspecialchars($st['full_name']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($st['username']); ?></code></td>
                                <td><?php echo htmlspecialchars($st['email']); ?></td>
                                <td><?php echo htmlspecialchars($st['phone']); ?></td>
                                <td>
                                    <?php if ($st['role'] === 'Admin'): ?>
                                        <span class="badge" style="background:#f3e8ff; color:#6b21a8;"><i class="fa-solid fa-shield-halved"></i> System Admin</span>
                                    <?php elseif ($st['role'] === 'Pharmacist'): ?>
                                        <span class="badge" style="background:#e0f2fe; color:#0369a1;"><i class="fa-solid fa-user-doctor"></i> Registered Pharmacist</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#f1f5f9; color:#475569;"><i class="fa-solid fa-user"></i> Counter Staff</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-in-stock"><?php echo htmlspecialchars($st['status']); ?></span></td>
                                <td>
                                    <div style="display:flex; gap:0.35rem;">
                                        <a href="edit_staff.php?id=<?php echo $st['id']; ?>" class="btn btn-sm btn-outline btn-icon" title="Edit Staff" style="color:var(--secondary);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline btn-icon" title="Delete Staff" style="color:var(--danger);" onclick="confirmDelete('<?php echo htmlspecialchars($st['full_name']); ?>', 'staff_list.php', <?php echo $st['id']; ?>)">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
