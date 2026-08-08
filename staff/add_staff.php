<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_name = htmlspecialchars($_POST['full_name'] ?? 'New Employee');
    header("Location: staff_list.php?msg=" . urlencode("Staff member '{$staff_name}' added successfully!"));
    exit;
}
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
