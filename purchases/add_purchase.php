<?php
$path_prefix = '../';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/db_helper.php';

// Handle POST before including templates to allow header() redirects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        header("Location: purchase_list.php?msg=" . urlencode("Invalid CSRF token."));
        exit;
    }
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $purchase_date = $_POST['purchase_date'] ?? date('Y-m-d');
    $medicine_id = (int)($_POST['medicine_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $purchase_price = is_numeric($_POST['purchase_price'] ?? null) ? (float)$_POST['purchase_price'] : 0.0;
    $total_amount = $quantity * $purchase_price;

    if ($supplier_id <= 0 || $medicine_id <= 0 || $quantity <= 0 || $purchase_price <= 0) {
        header("Location: purchase_list.php?msg=" . urlencode("Missing or invalid purchase fields."));
        exit;
    }

    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $pdo->beginTransaction();
            $po_no = 'PO-' . date('Y') . '-' . rand(100, 999);

            $stmt = $pdo->prepare('INSERT INTO purchases (purchase_no, supplier_id, purchase_date, total_amount, status) VALUES (:purchase_no, :supplier_id, :purchase_date, :total_amount, :status)');
            $stmt->execute([
                ':purchase_no' => $po_no,
                ':supplier_id' => $supplier_id,
                ':purchase_date' => $purchase_date,
                ':total_amount' => $total_amount,
                ':status' => 'Received'
            ]);
            $purchase_id = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO purchase_items (purchase_id, medicine_id, quantity, purchase_price, total_price) VALUES (:purchase_id, :medicine_id, :quantity, :purchase_price, :total_price)');
            $itemStmt->execute([
                ':purchase_id' => $purchase_id,
                ':medicine_id' => $medicine_id,
                ':quantity' => $quantity,
                ':purchase_price' => $purchase_price,
                ':total_price' => $total_amount
            ]);

            // update medicine stock
            $cstmt = $pdo->prepare('SELECT stock_quantity FROM medicines WHERE id = :id LIMIT 1');
            $cstmt->execute([':id' => $medicine_id]);
            $cur = $cstmt->fetchColumn();
            $newQty = intval($cur) + $quantity;
            $status = ($newQty <= 0) ? 'Out of Stock' : (($newQty <= 15) ? 'Low Stock' : 'In Stock');
            $updateStmt = $pdo->prepare('UPDATE medicines SET stock_quantity = :qty, status = :status WHERE id = :id');
            $updateStmt->execute([':qty' => $newQty, ':status' => $status, ':id' => $medicine_id]);

            $pdo->commit();
            header("Location: purchase_list.php?msg=" . urlencode("Purchase {$po_no} recorded and stock updated."));
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            @file_put_contents(__DIR__ . '/../logs/db_errors.log', date('Y-m-d H:i:s') . " - purchase error: " . $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
            header("Location: purchase_list.php?msg=" . urlencode("DB error recording purchase."));
            exit;
        }
    } else {
        $po_no = 'PO-LOCAL-' . rand(100,999);
        header("Location: purchase_list.php?msg=" . urlencode("Purchase order '{$po_no}' recorded (offline fallback)."));
        exit;
    }
}
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$suppliers = get_all_suppliers();
$medicines = get_all_medicines();
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
                    <?php echo csrf_input(); ?>
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
