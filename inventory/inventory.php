<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$medicines = get_all_medicines();

$filter_status = $_GET['status'] ?? 'all';
if ($filter_status !== 'all') {
    $medicines = array_filter($medicines, function($m) use ($filter_status) {
        return strtolower(str_replace(' ', '_', $m['status'])) === strtolower($filter_status);
    });
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-boxes-stacked" style="color:var(--primary);"></i> Inventory & Stock Management</h1>
                <p>Monitor warehouse and counter stock levels, low-stock warnings, and expiry tracking.</p>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" onclick="openStockAdjustmentModal()">
                    <i class="fa-solid fa-boxes-packing"></i> Adjust Stock Qty
                </button>
            </div>
        </div>

        <!-- Filter Tabs Bar -->
        <div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; flex-wrap:wrap;">
            <a href="inventory.php?status=all" class="btn btn-sm <?php echo ($filter_status == 'all') ? 'btn-primary' : 'btn-outline'; ?>">
                All Medicines (<?php echo count(get_all_medicines()); ?>)
            </a>
            <a href="inventory.php?status=in_stock" class="btn btn-sm <?php echo ($filter_status == 'in_stock') ? 'btn-success' : 'btn-outline'; ?>">
                <i class="fa-solid fa-circle-check"></i> In Stock
            </a>
            <a href="inventory.php?status=low_stock" class="btn btn-sm <?php echo ($filter_status == 'low_stock') ? 'btn-warning' : 'btn-outline'; ?>">
                <i class="fa-solid fa-triangle-exclamation"></i> Low Stock (2)
            </a>
            <a href="inventory.php?status=out_of_stock" class="btn btn-sm <?php echo ($filter_status == 'out_of_stock') ? 'btn-danger' : 'btn-outline'; ?>">
                <i class="fa-solid fa-circle-xmark"></i> Out of Stock (1)
            </a>
            <a href="inventory.php?status=expired" class="btn btn-sm <?php echo ($filter_status == 'expired') ? 'btn-secondary' : 'btn-outline'; ?>">
                <i class="fa-solid fa-ban"></i> Expired (1)
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-warehouse"></i> Inventory Stock Audit Table
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <input type="text" id="invSearchInput" class="form-control" style="width:240px; padding:0.4rem 0.8rem;" placeholder="Search inventory..." onkeyup="filterTable('invSearchInput', 'inventoryTable')">
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Medicine Name</th>
                                <th>Category</th>
                                <th>Batch #</th>
                                <th>Expiry Date</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Current Qty</th>
                                <th>Stock Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicines as $med): ?>
                            <tr>
                                <td><code>MED-00<?php echo $med['id']; ?></code></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($med['medicine_name']); ?></strong>
                                    <div style="font-size:0.75rem; color:#64748b;"><?php echo htmlspecialchars($med['generic_name']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($med['category_name']); ?></td>
                                <td><code><?php echo htmlspecialchars($med['batch_number']); ?></code></td>
                                <td><?php echo $med['expiry_date']; ?></td>
                                <td>$<?php echo number_format($med['purchase_price'], 2); ?></td>
                                <td><strong>$<?php echo number_format($med['selling_price'], 2); ?></strong></td>
                                <td>
                                    <strong style="font-size:1rem;"><?php echo $med['stock_quantity']; ?> units</strong>
                                </td>
                                <td>
                                    <?php 
                                    if ($med['status'] == 'In Stock') echo '<span class="badge badge-in-stock"><i class="fa-solid fa-check"></i> In Stock</span>';
                                    else if ($med['status'] == 'Low Stock') echo '<span class="badge badge-low-stock"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>';
                                    else if ($med['status'] == 'Out of Stock') echo '<span class="badge badge-out-stock"><i class="fa-solid fa-xmark"></i> Out of Stock</span>';
                                    else echo '<span class="badge badge-expired"><i class="fa-solid fa-ban"></i> Expired</span>';
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline" onclick="openItemStockAdjustModal('<?php echo htmlspecialchars($med['medicine_name']); ?>', <?php echo $med['stock_quantity']; ?>)">
                                        <i class="fa-solid fa-arrows-up-down"></i> Adjust Qty
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
function openStockAdjustmentModal() {
    openModal(
        'Quick Stock Adjustment',
        `<form id="stockForm">
            <div class="form-group">
                <label class="form-label">Select Medicine Item</label>
                <select class="form-control">
                    <option>Napa 500mg (SQ-N2024-01)</option>
                    <option>Seclo 20mg (SQ-S2024-88)</option>
                    <option>Omeprazole 20mg (RN-OMP-33)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Adjustment Mode</label>
                <select class="form-control">
                    <option value="add">Add Received Stock (+)</option>
                    <option value="subtract">Deduct / Damaged (-)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" value="50">
            </div>
            <div class="form-group">
                <label class="form-label">Reason / Reference</label>
                <input type="text" class="form-control" placeholder="e.g. Audit re-count / Stock refill">
            </div>
        </form>`,
        `<button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
         <button class="btn btn-primary" onclick="showToast('Stock adjusted successfully!', 'success'); closeModal();">Save Adjustment</button>`
    );
}

function openItemStockAdjustModal(name, currQty) {
    openModal(
        `Adjust Stock - ${name}`,
        `<p style="margin-bottom:1rem;">Current Available Stock: <strong>${currQty} units</strong></p>
         <div class="form-group">
             <label class="form-label">New Stock Quantity</label>
             <input type="number" class="form-control" value="${currQty}">
         </div>
         <div class="form-group">
             <label class="form-label">Audit Notes</label>
             <input type="text" class="form-control" placeholder="Reason for change">
         </div>`,
        `<button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
         <button class="btn btn-primary" onclick="showToast('Stock level updated!', 'success'); closeModal();">Update Stock</button>`
    );
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
