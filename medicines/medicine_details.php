<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$med_id = $_GET['id'] ?? 1;
$medicines = get_all_medicines();
$target_med = null;

foreach ($medicines as $m) {
    if ($m['id'] == $med_id) {
        $target_med = $m;
        break;
    }
}
if (!$target_med) $target_med = $medicines[0];
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-circle-info" style="color:var(--primary);"></i> Medicine Profile Details</h1>
                <p>Complete information for <strong><?php echo htmlspecialchars($target_med['medicine_name']); ?></strong></p>
            </div>
            <div class="page-actions">
                <a href="edit_medicine.php?id=<?php echo $target_med['id']; ?>" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Product
                </a>
                <a href="medicine_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;">
            <!-- Left Info Panel -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-pills"></i> Core Specifications
                    </div>
                    <?php 
                    if ($target_med['status'] == 'In Stock') echo '<span class="badge badge-in-stock">In Stock</span>';
                    else if ($target_med['status'] == 'Low Stock') echo '<span class="badge badge-low-stock">Low Stock</span>';
                    else if ($target_med['status'] == 'Out of Stock') echo '<span class="badge badge-out-stock">Out of Stock</span>';
                    else echo '<span class="badge badge-expired">Expired</span>';
                    ?>
                </div>
                <div class="card-body">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                        <div>
                            <span style="font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:700;">Brand Name</span>
                            <h2 style="color:var(--primary); font-size:1.5rem;"><?php echo htmlspecialchars($target_med['medicine_name']); ?></h2>
                        </div>
                        <div>
                            <span style="font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:700;">Generic Composition</span>
                            <h3 style="color:var(--text-primary); font-size:1.2rem; font-style:italic;"><?php echo htmlspecialchars($target_med['generic_name']); ?></h3>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem; padding:1rem; background:#f8fafc; border-radius:8px; border:1px solid var(--border-color); margin-bottom:1.5rem;">
                        <div>
                            <span style="font-size:0.75rem; color:#64748b;">Category</span>
                            <p style="font-weight:600;"><?php echo htmlspecialchars($target_med['category_name']); ?></p>
                        </div>
                        <div>
                            <span style="font-size:0.75rem; color:#64748b;">Manufacturer</span>
                            <p style="font-weight:600;"><?php echo htmlspecialchars($target_med['manufacturer']); ?></p>
                        </div>
                        <div>
                            <span style="font-size:0.75rem; color:#64748b;">Batch Number</span>
                            <p><code><?php echo htmlspecialchars($target_med['batch_number']); ?></code></p>
                        </div>
                    </div>

                    <div style="margin-bottom:1.5rem;">
                        <h4 style="font-size:0.95rem; margin-bottom:0.4rem;"><i class="fa-solid fa-file-lines"></i> Description & Dosage Notes</h4>
                        <p style="color:#475569; font-size:0.9rem; line-height:1.6; background:#fff; padding:0.85rem; border-radius:6px; border:1px solid var(--border-color);">
                            <?php echo htmlspecialchars($target_med['description']); ?>
                        </p>
                    </div>

                    <!-- Barcode Visual Generator Mock -->
                    <div>
                        <h4 style="font-size:0.95rem; margin-bottom:0.6rem;"><i class="fa-solid fa-barcode"></i> Inventory Barcode (GTIN-13)</h4>
                        <div style="background:#fff; padding:1rem; border:1px solid var(--border-color); border-radius:6px; text-align:center;">
                            <div style="letter-spacing: 4px; font-family: monospace; font-size:1.8rem; font-weight:bold; color:#0f172a;">
                                ||||| | |||| ||| |||| | |||||
                            </div>
                            <span style="font-family:monospace; font-size:0.8rem; color:#64748b;">890108700<?php echo sprintf("%04d", $target_med['id']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Financial & Stock Panel -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-coins"></i> Commercial Pricing
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
                            <span style="color:#64748b;">Purchase Price:</span>
                            <strong>$<?php echo number_format($target_med['purchase_price'], 2); ?></strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
                            <span style="color:#64748b;">Selling Price:</span>
                            <strong style="color:var(--primary); font-size:1.2rem;">$<?php echo number_format($target_med['selling_price'], 2); ?></strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:1rem; padding-top:0.5rem; border-top:1px dashed var(--border-color);">
                            <span style="color:#64748b;">Gross Profit Margin:</span>
                            <strong style="color:var(--success);">$<?php echo number_format($target_med['selling_price'] - $target_med['purchase_price'], 2); ?></strong>
                        </div>

                        <a href="../sales/new_sale.php" class="btn btn-primary" style="width:100%;">
                            <i class="fa-solid fa-cart-plus"></i> Open POS & Sell Item
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-truck-field"></i> Supplier & Stock
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom:0.75rem;">
                            <span style="font-size:0.75rem; color:#64748b;">Vendor / Supplier</span>
                            <p style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($target_med['supplier_name']); ?></p>
                        </div>
                        <div style="margin-bottom:0.75rem;">
                            <span style="font-size:0.75rem; color:#64748b;">Available Units</span>
                            <p style="font-size:1.2rem; font-weight:700;"><?php echo $target_med['stock_quantity']; ?> Units</p>
                        </div>
                        <div>
                            <span style="font-size:0.75rem; color:#64748b;">Batch Expiry Date</span>
                            <p style="font-weight:600; color:var(--danger);"><?php echo $target_med['expiry_date']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
