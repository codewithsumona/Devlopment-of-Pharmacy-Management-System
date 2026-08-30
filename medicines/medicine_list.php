<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

 $medicines = get_all_medicines();
 // Handle delete action via POST with CSRF
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
                 $stmt = $pdo->prepare("DELETE FROM medicines WHERE id = :id");
                 $stmt->execute([':id' => $delId]);
                 $action_msg = "Medicine #{$delId} deleted successfully.";
             } catch (Exception $e) {
                 $action_msg = "Unable to delete medicine via DB (prototype fallback).";
             }
         } else {
             $action_msg = "Delete simulated (offline fallback) for Medicine #{$delId}.";
         }
     }
     header('Location: medicine_list.php?msg=' . urlencode($action_msg));
     exit;
 }

// Success Notification handle
if (isset($_GET['msg'])) {
    $action_msg = htmlspecialchars($_GET['msg']);
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-pills" style="color:var(--primary);"></i> Medicine Management</h1>
                <p>View, search, filter and manage pharmacy drug inventory catalog.</p>
            </div>
            <div class="page-actions">
                <a href="add_medicine.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add New Medicine
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
                    <i class="fa-solid fa-list-check"></i> Master Medicine Catalog
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <input type="text" id="medSearchInput" class="form-control" style="width:240px; padding:0.4rem 0.8rem;" placeholder="Filter medicines..." onkeyup="filterTable('medSearchInput', 'medicineTable')">
                    <button class="btn btn-sm btn-outline" onclick="triggerReportDemo('CSV Catalog Export')"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
                    <button class="btn btn-sm btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table" id="medicineTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Medicine Name</th>
                                <th>Generic Name</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Batch No</th>
                                <th>Expiry</th>
                                <th>Cost</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicines as $med): ?>
                            <tr>
                                <td>#<?php echo $med['id']; ?></td>
                                <td>
                                    <strong style="color:var(--text-primary); font-size:0.95rem;"><?php echo htmlspecialchars($med['medicine_name']); ?></strong>
                                </td>
                                <td><em style="color:#64748b; font-size:0.85rem;"><?php echo htmlspecialchars($med['generic_name']); ?></em></td>
                                <td><?php echo htmlspecialchars($med['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($med['manufacturer']); ?></td>
                                <td><code><?php echo htmlspecialchars($med['batch_number']); ?></code></td>
                                <td><?php echo $med['expiry_date']; ?></td>
                                <td>$<?php echo number_format($med['purchase_price'], 2); ?></td>
                                <td><strong>$<?php echo number_format($med['selling_price'], 2); ?></strong></td>
                                <td>
                                    <?php if ($med['stock_quantity'] <= 0): ?>
                                        <strong style="color:var(--danger);">0</strong>
                                    <?php elseif ($med['stock_quantity'] <= 15): ?>
                                        <strong style="color:var(--warning);"><?php echo $med['stock_quantity']; ?></strong>
                                    <?php else: ?>
                                        <span><?php echo $med['stock_quantity']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($med['status'] == 'In Stock') echo '<span class="badge badge-in-stock">In Stock</span>';
                                    else if ($med['status'] == 'Low Stock') echo '<span class="badge badge-low-stock">Low Stock</span>';
                                    else if ($med['status'] == 'Out of Stock') echo '<span class="badge badge-out-stock">Out of Stock</span>';
                                    else echo '<span class="badge badge-expired">Expired</span>';
                                    ?>
                                </td>
                                <td>
                                    <div style="display:flex; gap:0.35rem;">
                                        <a href="medicine_details.php?id=<?php echo $med['id']; ?>" class="btn btn-sm btn-outline btn-icon" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="edit_medicine.php?id=<?php echo $med['id']; ?>" class="btn btn-sm btn-outline btn-icon" title="Edit Medicine" style="color:var(--secondary);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline btn-icon" title="Delete Medicine" style="color:var(--danger);" onclick="confirmDelete('<?php echo htmlspecialchars($med['medicine_name']); ?>', 'medicine_list.php', <?php echo $med['id']; ?>)">
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
