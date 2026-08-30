<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$suppliers = get_all_suppliers();

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
                $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = :id");
                $stmt->execute([':id' => $delId]);
                $action_msg = "Supplier #{$delId} deleted successfully.";
            } catch (Exception $e) {
                $action_msg = "Unable to delete supplier via DB (prototype fallback).";
            }
        } else {
            $action_msg = "Delete simulated (offline fallback) for Supplier #{$delId}.";
        }
    }
    header('Location: supplier_list.php?msg=' . urlencode($action_msg));
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
                <h1><i class="fa-solid fa-building-user" style="color:var(--primary);"></i> Supplier Management</h1>
                <p>Manage pharmaceutical vendors, distributors, and manufacturer contacts.</p>
            </div>
            <div class="page-actions">
                <a href="add_supplier.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add New Supplier
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
                    <i class="fa-solid fa-address-book"></i> Active Supplier Directory
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <input type="text" id="supSearchInput" class="form-control" style="width:240px; padding:0.4rem 0.8rem;" placeholder="Search supplier..." onkeyup="filterTable('supSearchInput', 'supplierTable')">
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table" id="supplierTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Supplier Name</th>
                                <th>Company / Brand</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $sup): ?>
                            <tr>
                                <td>#SUP-0<?php echo $sup['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($sup['supplier_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sup['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($sup['phone']); ?></td>
                                <td><?php echo htmlspecialchars($sup['email']); ?></td>
                                <td style="max-width:200px; font-size:0.8rem;"><?php echo htmlspecialchars($sup['address']); ?></td>
                                <td><span class="badge badge-in-stock"><?php echo htmlspecialchars($sup['status']); ?></span></td>
                                <td>
                                    <div style="display:flex; gap:0.35rem;">
                                        <a href="edit_supplier.php?id=<?php echo $sup['id']; ?>" class="btn btn-sm btn-outline btn-icon" title="Edit Supplier" style="color:var(--secondary);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline btn-icon" title="Delete Supplier" style="color:var(--danger);" onclick="confirmDelete('<?php echo htmlspecialchars($sup['supplier_name']); ?>', 'supplier_list.php', <?php echo $sup['id']; ?>)">
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
