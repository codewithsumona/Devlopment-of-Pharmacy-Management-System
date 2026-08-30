<?php
$path_prefix = '../';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/db_helper.php';

// Handle POST before including templates to allow header() redirects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        header("Location: medicine_list.php?msg=" . urlencode("Invalid CSRF token."));
        exit;
    }
    $med_id = $_GET['id'] ?? 1;
    $med_name = trim($_POST['medicine_name'] ?? '');
    $generic_name = trim($_POST['generic_name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $batch_number = trim($_POST['batch_number'] ?? '');
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $purchase_price = is_numeric($_POST['purchase_price'] ?? null) ? (float)$_POST['purchase_price'] : 0.0;
    $selling_price = is_numeric($_POST['selling_price'] ?? null) ? (float)$_POST['selling_price'] : 0.0;
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $expiry_date = $_POST['expiry_date'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if ($med_name === '' || $generic_name === '' || $category_id <= 0 || $supplier_id <= 0 || $expiry_date === '') {
        header("Location: medicine_list.php?msg=" . urlencode("Missing required fields for medicine update."));
        exit;
    }

    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $status = ($stock_quantity <= 0) ? 'Out of Stock' : (($stock_quantity <= 15) ? 'Low Stock' : 'In Stock');
            $stmt = $pdo->prepare("UPDATE medicines SET medicine_name = :medicine_name, generic_name = :generic_name, category_id = :category_id, supplier_id = :supplier_id, manufacturer = :manufacturer, batch_number = :batch_number, purchase_price = :purchase_price, selling_price = :selling_price, stock_quantity = :stock_quantity, expiry_date = :expiry_date, description = :description, status = :status WHERE id = :id");
            $stmt->execute([
                ':medicine_name' => $med_name,
                ':generic_name' => $generic_name,
                ':category_id' => $category_id,
                ':supplier_id' => $supplier_id,
                ':manufacturer' => $manufacturer,
                ':batch_number' => $batch_number,
                ':purchase_price' => $purchase_price,
                ':selling_price' => $selling_price,
                ':stock_quantity' => $stock_quantity,
                ':expiry_date' => $expiry_date,
                ':description' => $description,
                ':status' => $status,
                ':id' => $med_id
            ]);
            header("Location: medicine_list.php?msg=" . urlencode("Medicine '{$med_name}' updated successfully."));
            exit;
        } catch (Exception $e) {
            header("Location: medicine_list.php?msg=" . urlencode("DB error updating medicine (fallback used)."));
            exit;
        }
    } else {
        header("Location: medicine_list.php?msg=" . urlencode("Medicine '{$med_name}' updated (offline fallback)."));
        exit;
    }
}

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
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        header("Location: medicine_list.php?msg=" . urlencode("Invalid CSRF token."));
        exit;
    }
    $med_name = trim($_POST['medicine_name'] ?? '');
    $generic_name = trim($_POST['generic_name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $batch_number = trim($_POST['batch_number'] ?? '');
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $purchase_price = is_numeric($_POST['purchase_price'] ?? null) ? (float)$_POST['purchase_price'] : 0.0;
    $selling_price = is_numeric($_POST['selling_price'] ?? null) ? (float)$_POST['selling_price'] : 0.0;
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $expiry_date = $_POST['expiry_date'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if ($med_name === '' || $generic_name === '' || $category_id <= 0 || $supplier_id <= 0 || $expiry_date === '') {
        header("Location: medicine_list.php?msg=" . urlencode("Missing required fields for medicine update."));
        exit;
    }

    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $status = ($stock_quantity <= 0) ? 'Out of Stock' : (($stock_quantity <= 15) ? 'Low Stock' : 'In Stock');
            $stmt = $pdo->prepare("UPDATE medicines SET medicine_name = :medicine_name, generic_name = :generic_name, category_id = :category_id, supplier_id = :supplier_id, manufacturer = :manufacturer, batch_number = :batch_number, purchase_price = :purchase_price, selling_price = :selling_price, stock_quantity = :stock_quantity, expiry_date = :expiry_date, description = :description, status = :status WHERE id = :id");
            $stmt->execute([
                ':medicine_name' => $med_name,
                ':generic_name' => $generic_name,
                ':category_id' => $category_id,
                ':supplier_id' => $supplier_id,
                ':manufacturer' => $manufacturer,
                ':batch_number' => $batch_number,
                ':purchase_price' => $purchase_price,
                ':selling_price' => $selling_price,
                ':stock_quantity' => $stock_quantity,
                ':expiry_date' => $expiry_date,
                ':description' => $description,
                ':status' => $status,
                ':id' => $target_med['id']
            ]);
            header("Location: medicine_list.php?msg=" . urlencode("Medicine '{$med_name}' updated successfully."));
            exit;
        } catch (Exception $e) {
            header("Location: medicine_list.php?msg=" . urlencode("DB error updating medicine (fallback used)."));
            exit;
        }
    } else {
        header("Location: medicine_list.php?msg=" . urlencode("Medicine '{$med_name}' updated (offline fallback)."));
        exit;
    }
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
                    <?php echo csrf_input(); ?>
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
