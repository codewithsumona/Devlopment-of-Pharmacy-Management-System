<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$purchases = get_all_purchases();

// Handle delete action
$action_msg = '';
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM purchases WHERE id = :id");
            $stmt->execute([':id' => $delId]);
            $action_msg = "Purchase order #{$delId} deleted successfully.";
        } catch (Exception $e) {
            $action_msg = "Unable to delete purchase via DB (prototype fallback).";
        }
    } else {
        $action_msg = "Delete simulated (offline fallback) for Purchase #{$delId}.";
    }
    header('Location: purchase_list.php?msg=' . urlencode($action_msg));
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
                <h1><i class="fa-solid fa-truck-field" style="color:var(--primary);"></i> Stock Purchase Management</h1>
                <p>Track vendor procurement orders, incoming stock inventory, and supplier bills.</p>
            </div>
            <div class="page-actions">
                <a href="add_purchase.php" class="btn btn-primary">
                    <i class="fa-solid fa-cart-plus"></i> Add New Purchase Order
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
                    <i class="fa-solid fa-boxes-packing"></i> Purchase Orders Ledger
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <input type="text" id="purchaseSearchInput" class="form-control" style="width:240px; padding:0.4rem 0.8rem;" placeholder="Search PO # or supplier..." onkeyup="filterTable('purchaseSearchInput', 'purchaseTable')">
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table" id="purchaseTable">
                        <thead>
                            <tr>
                                <th>Purchase ID</th>
                                <th>Supplier Company</th>
                                <th>Order Date</th>
                                <th>Items / Qty</th>
                                <th>Total Cost ($)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchases as $p): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($p['purchase_no']); ?></code></td>
                                <td><strong><?php echo htmlspecialchars($p['supplier_name']); ?></strong></td>
                                <td><?php echo $p['purchase_date']; ?></td>
                                <td><?php echo $p['total_items']; ?> Units</td>
                                <td><strong style="color:var(--primary-dark);">$<?php echo number_format($p['total_amount'], 2); ?></strong></td>
                                <td>
                                    <?php if ($p['status'] === 'Received'): ?>
                                        <span class="badge badge-in-stock"><i class="fa-solid fa-check"></i> Received</span>
                                    <?php else: ?>
                                        <span class="badge badge-low-stock"><i class="fa-solid fa-clock"></i> Pending Arrival</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline" onclick="openPurchaseDetailsModal('<?php echo htmlspecialchars($p['purchase_no']); ?>', '<?php echo htmlspecialchars($p['supplier_name']); ?>', '<?php echo number_format($p['total_amount'], 2); ?>')">
                                        <i class="fa-solid fa-eye"></i> Details
                                    </button>
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

<script>
function openPurchaseDetailsModal(poNo, supplier, total) {
    openModal(
        `Purchase Order Details - ${poNo}`,
        `<div style="display:flex; flex-direction:column; gap:0.75rem;">
            <p><strong>Vendor Supplier:</strong> ${supplier}</p>
            <p><strong>Total Bill Amount:</strong> $${total}</p>
            <hr>
            <h4>Items Breakdown</h4>
            <table class="table" style="font-size:0.85rem;">
                <thead><tr><th>Medicine</th><th>Qty</th><th>Cost</th></tr></thead>
                <tbody>
                    <tr><td>Napa 500mg Batch SQ-N2024-01</td><td>450</td><td>$1.10</td></tr>
                    <tr><td>Azithrocin 500mg Batch BX-AZ-771</td><td>120</td><td>$28.00</td></tr>
                </tbody>
            </table>
        </div>`,
        `<button class="btn btn-primary" onclick="closeModal()">Close</button>`
    );
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
