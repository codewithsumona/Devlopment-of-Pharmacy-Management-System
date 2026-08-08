<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$suppliers = get_all_suppliers();
$categories = [
    ['id' => 1, 'category_name' => 'Analgesics / Antipyretics'],
    ['id' => 2, 'category_name' => 'Gastric / Anti-ulcerants'],
    ['id' => 3, 'category_name' => 'Antihistamines'],
    ['id' => 4, 'category_name' => 'Antibiotics'],
    ['id' => 5, 'category_name' => 'Laxatives / Syrup'],
    ['id' => 6, 'category_name' => 'Cardiovascular']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $med_name = htmlspecialchars($_POST['medicine_name'] ?? 'New Medicine');
    header("Location: medicine_list.php?msg=" . urlencode("Medicine '{$med_name}' successfully added to catalog!"));
    exit;
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-plus-circle" style="color:var(--primary);"></i> Add New Medicine</h1>
                <p>Register a new pharmaceutical item into system database inventory.</p>
            </div>
            <div class="page-actions">
                <a href="medicine_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-prescription-bottle-medical"></i> Medicine Information Form
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="add_medicine.php" id="addMedForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Medicine Brand Name *</label>
                            <input type="text" name="medicine_name" class="form-control" placeholder="e.g. Napa Extra 500mg" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Generic Name / Composition *</label>
                            <input type="text" name="generic_name" class="form-control" placeholder="e.g. Paracetamol + Caffeine" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Therapeutic Category *</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pharmaceutical Manufacturer *</label>
                            <input type="text" name="manufacturer" class="form-control" placeholder="e.g. Square Pharmaceuticals Ltd." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Batch Number *</label>
                            <input type="text" name="batch_number" class="form-control" placeholder="e.g. SQ-2026-99" required>
                        </div>

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
                            <label class="form-label">Purchase Unit Cost ($) *</label>
                            <input type="number" step="0.01" name="purchase_price" id="buyPrice" class="form-control" placeholder="0.00" oninput="calcMargin()" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Selling Retail Price ($) *</label>
                            <input type="number" step="0.01" name="selling_price" id="sellPrice" class="form-control" placeholder="0.00" oninput="calcMargin()" required>
                            <span id="marginBadge" style="font-size:0.75rem; color:var(--primary); font-weight:600; margin-top:0.2rem;"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Initial Stock Quantity *</label>
                            <input type="number" name="stock_quantity" class="form-control" placeholder="100" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Expiry Date *</label>
                            <input type="date" name="expiry_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:1rem;">
                        <label class="form-label">Dosage & Description Details</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Provide dosage instructions, side effects, storage temp, etc."></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="medicine_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="reset" class="btn btn-outline">Reset Form</button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Save Medicine Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calcMargin() {
    const buy = parseFloat(document.getElementById('buyPrice').value) || 0;
    const sell = parseFloat(document.getElementById('sellPrice').value) || 0;
    const badge = document.getElementById('marginBadge');

    if (buy > 0 && sell > 0) {
        const profit = sell - buy;
        const margin = ((profit / buy) * 100).toFixed(1);
        badge.innerHTML = `<i class="fa-solid fa-chart-line"></i> Est. Profit: $${profit.toFixed(2)} (${margin}% markup)`;
    } else {
        badge.innerHTML = '';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
