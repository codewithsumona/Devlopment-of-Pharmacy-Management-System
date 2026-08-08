<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$suppliers = get_all_suppliers();
$medicines = get_all_medicines();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $po_no = 'PO-2026-' . rand(100, 999);
    header("Location: purchase_list.php?msg=" . urlencode("Purchase order '{$po_no}' recorded successfully!"));
    exit;
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-plus-circle" style="color:var(--primary);"></i> Create Stock Purchase Entry</h1>
                <p>Record stock replenishment orders from pharmaceutical manufacturers & distributors.</p>
            </div>
            <div class="page-actions">
                <a href="purchase_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-truck-ramp-box"></i> Purchase Order Form
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="add_purchase.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Supplier Company *</label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">Select Vendor...</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?php echo $sup['id']; ?>"><?php echo htmlspecialchars($sup['supplier_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Order Date *</label>
                            <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Select Medicine *</label>
                            <select name="medicine_id" class="form-control" id="poMedSelect" onchange="updatePoCalc()" required>
                                <option value="">Select Medicine Item...</option>
                                <?php foreach ($medicines as $med): ?>
                                    <option value="<?php echo $med['id']; ?>" data-cost="<?php echo $med['purchase_price']; ?>">
                                        <?php echo htmlspecialchars($med['medicine_name']); ?> (Cost: $<?php echo $med['purchase_price']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quantity Units *</label>
                            <input type="number" name="quantity" id="poQty" class="form-control" value="100" min="1" oninput="updatePoCalc()" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Unit Cost Price ($) *</label>
                            <input type="number" step="0.01" name="purchase_price" id="poCost" class="form-control" placeholder="0.00" oninput="updatePoCalc()" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Calculated Order Total ($)</label>
                            <input type="text" name="total_amount" id="poTotal" class="form-control" style="font-weight:bold; color:var(--primary-dark);" readonly placeholder="$0.00">
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="purchase_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Submit Purchase Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePoCalc() {
    const select = document.getElementById('poMedSelect');
    const option = select.options[select.selectedIndex];
    const costInput = document.getElementById('poCost');
    
    if (option && option.getAttribute('data-cost') && (!costInput.value || costInput.value == 0)) {
        costInput.value = option.getAttribute('data-cost');
    }

    const qty = parseFloat(document.getElementById('poQty').value) || 0;
    const cost = parseFloat(costInput.value) || 0;
    const total = qty * cost;

    document.getElementById('poTotal').value = '$' + total.toFixed(2);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
