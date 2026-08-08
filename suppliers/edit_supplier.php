<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sup_id = $_GET['id'] ?? 1;
$suppliers = get_all_suppliers();
$target_sup = null;

foreach ($suppliers as $s) {
    if ($s['id'] == $sup_id) {
        $target_sup = $s;
        break;
    }
}
if (!$target_sup) $target_sup = $suppliers[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sup_name = htmlspecialchars($_POST['supplier_name'] ?? 'Supplier');
    header("Location: supplier_list.php?msg=" . urlencode("Supplier '{$sup_name}' updated successfully!"));
    exit;
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-pen-to-square" style="color:var(--secondary);"></i> Edit Supplier Profile</h1>
                <p>Modify vendor contact details for <strong><?php echo htmlspecialchars($target_sup['supplier_name']); ?></strong></p>
            </div>
            <div class="page-actions">
                <a href="supplier_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Directory
                </a>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-building"></i> Edit Vendor Record (SUP-0<?php echo $target_sup['id']; ?>)
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="edit_supplier.php?id=<?php echo $target_sup['id']; ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Full Supplier Name *</label>
                            <input type="text" name="supplier_name" class="form-control" value="<?php echo htmlspecialchars($target_sup['supplier_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Company Brand Name *</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($target_sup['company_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Phone *</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($target_sup['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Email *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($target_sup['email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:1rem;">
                        <label class="form-label">Office & Warehouse Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($target_sup['address']); ?></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="supplier_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Update Supplier Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
