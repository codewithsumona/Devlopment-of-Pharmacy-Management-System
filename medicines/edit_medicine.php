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
    $med_name = htmlspecialchars($_POST['medicine_name'] ?? 'Medicine');
    header("Location: medicine_list.php?msg=" . urlencode("Medicine '{$med_name}' updated successfully!"));
    exit;
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-pen-to-square" style="color:var(--secondary);"></i> Edit Medicine Record</h1>
                <p>Modify inventory information for <strong><?php echo htmlspecialchars($target_med['medicine_name']); ?></strong></p>
            </div>
            <div class="page-actions">
                <a href="medicine_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Catalog
                </a>
            </div>
        </div>

        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-pen"></i> Edit Product Details (ID: #<?php echo $target_med['id']; ?>)
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="edit_medicine.php?id=<?php echo $target_med['id']; ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Medicine Brand Name *</label>
                            <input type="text" name="medicine_name" class="form-control" value="<?php echo htmlspecialchars($target_med['medicine_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Generic Name / Composition *</label>
                            <input type="text" name="generic_name" class="form-control" value="<?php echo htmlspecialchars($target_med['generic_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Therapeutic Category *</label>
                            <select name="category_id" class="form-control" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['category_name'] == $target_med['category_name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pharmaceutical Manufacturer *</label>
                            <input type="text" name="manufacturer" class="form-control" value="<?php echo htmlspecialchars($target_med['manufacturer']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Batch Number *</label>
                            <input type="text" name="batch_number" class="form-control" value="<?php echo htmlspecialchars($target_med['batch_number']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Supplier Company *</label>
                            <select name="supplier_id" class="form-control" required>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?php echo $sup['id']; ?>" <?php echo ($sup['supplier_name'] == $target_med['supplier_name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sup['supplier_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Purchase Unit Cost ($) *</label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control" value="<?php echo $target_med['purchase_price']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Selling Retail Price ($) *</label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="<?php echo $target_med['selling_price']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" class="form-control" value="<?php echo $target_med['stock_quantity']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Expiry Date *</label>
                            <input type="date" name="expiry_date" class="form-control" value="<?php echo $target_med['expiry_date']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:1rem;">
                        <label class="form-label">Description & Notes</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($target_med['description']); ?></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="medicine_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Update Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
