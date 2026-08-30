<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

function save_local_sale_record(array $saleData): array {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/sales_log.json';
    $sales = [];
    if (file_exists($logFile)) {
        $raw = @file_get_contents($logFile);
        if ($raw !== false) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $sales = $decoded;
            }
        }
    }

    $saleRecord = [
        'id' => count($sales) + 1,
        'invoice_no' => $saleData['invoice_no'],
        'customer_name' => $saleData['customer_name'],
        'customer_phone' => $saleData['customer_phone'] ?? '',
        'pharmacist_id' => $saleData['pharmacist_id'],
        'pharmacist_name' => $saleData['pharmacist_name'] ?? 'System User',
        'subtotal' => number_format((float)$saleData['subtotal'], 2, '.', ''),
        'discount' => number_format((float)$saleData['discount'], 2, '.', ''),
        'grand_total' => number_format((float)$saleData['grand_total'], 2, '.', ''),
        'payment_method' => $saleData['payment_method'],
        'payment_status' => 'Paid',
        'sale_date' => $saleData['sale_date'],
        'items' => $saleData['items'] ?? []
    ];

    $sales[] = $saleRecord;
    @file_put_contents($logFile, json_encode($sales, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    return $saleRecord;
}

$pdo = getDBConnection();

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || empty($input)) {
    $input = $_POST;
    if (!is_array($input) || empty($input)) {
        parse_str(file_get_contents('php://input'), $input);
    }
}

if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request payload']);
    exit;
}

$items = $input['items'] ?? [];
if (!is_array($items)) {
    $items = [];
}

$customer_name = trim((string)($input['customer_name'] ?? 'Walk-in Customer'));
$payment_method = trim((string)($input['payment_method'] ?? 'Cash'));
$discount_percent = floatval($input['discount_percent'] ?? 0);
$subtotal = floatval($input['subtotal'] ?? 0);
$grand_total = floatval($input['grand_total'] ?? 0);
$pharmacist_id = $_SESSION['user_id'] ?? 1;
$pharmacist_name = $_SESSION['user_name'] ?? 'System User';

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'No items provided. Please add at least one medicine to the cart.']);
    exit;
}

try {
    if ($pdo !== false) {
        $pdo->beginTransaction();

        $prefix = 'INV-' . date('Y');
        $rand = rand(1000, 9999);
        $invoice_no = $prefix . '-' . $rand;

        $stmt = $pdo->prepare('INSERT INTO sales (invoice_no, customer_name, customer_phone, pharmacist_id, subtotal, discount, grand_total, payment_method, payment_status) VALUES (:invoice_no, :customer_name, :customer_phone, :pharmacist_id, :subtotal, :discount, :grand_total, :payment_method, :payment_status)');
        $stmt->execute([
            ':invoice_no' => $invoice_no,
            ':customer_name' => $customer_name,
            ':customer_phone' => '',
            ':pharmacist_id' => $pharmacist_id,
            ':subtotal' => $subtotal,
            ':discount' => ($subtotal * $discount_percent) / 100.0,
            ':grand_total' => $grand_total,
            ':payment_method' => $payment_method,
            ':payment_status' => 'Paid'
        ]);

        $sale_id = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO sale_items (sale_id, medicine_id, quantity, unit_price, total_price) VALUES (:sale_id, :medicine_id, :quantity, :unit_price, :total_price)');
        $updateMedStmt = $pdo->prepare('UPDATE medicines SET stock_quantity = GREATEST(stock_quantity - :qty, 0), status = :status WHERE id = :id');

        foreach ($items as $it) {
            $medId = (int)($it['medicine_id'] ?? 0);
            $qty = (int)($it['quantity'] ?? 0);
            $unit = floatval($it['unit_price'] ?? 0);
            $total = $unit * $qty;

            if ($medId <= 0 || $qty <= 0) {
                continue;
            }

            $itemStmt->execute([
                ':sale_id' => $sale_id,
                ':medicine_id' => $medId,
                ':quantity' => $qty,
                ':unit_price' => $unit,
                ':total_price' => $total
            ]);

            $cstmt = $pdo->prepare('SELECT stock_quantity FROM medicines WHERE id = :id LIMIT 1');
            $cstmt->execute([':id' => $medId]);
            $cur = $cstmt->fetchColumn();
            $newQty = max(0, intval($cur) - $qty);
            $status = ($newQty <= 0) ? 'Out of Stock' : (($newQty <= 15) ? 'Low Stock' : 'In Stock');

            $updateMedStmt->execute([':qty' => $qty, ':status' => $status, ':id' => $medId]);
        }

        $pdo->commit();

        $sale_date = date('Y-m-d H:i:s');
        echo json_encode(['success' => true, 'invoice_no' => $invoice_no, 'sale_date' => $sale_date, 'grand_total' => $grand_total, 'offline' => false]);
        exit;
    }

    // Fallback path when DB is unavailable: save the transaction locally so reports still work.
    $invoice_no = 'INV-OFFLINE-' . date('YmdHis');
    $sale_date = date('Y-m-d H:i:s');
    $discount = ($subtotal * $discount_percent) / 100.0;
    $saleRecord = save_local_sale_record([
        'invoice_no' => $invoice_no,
        'customer_name' => $customer_name,
        'customer_phone' => '',
        'pharmacist_id' => $pharmacist_id,
        'pharmacist_name' => $pharmacist_name,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'grand_total' => $grand_total,
        'payment_method' => $payment_method,
        'sale_date' => $sale_date,
        'items' => $items
    ]);

    echo json_encode([
        'success' => true,
        'invoice_no' => $invoice_no,
        'sale_date' => $sale_date,
        'grand_total' => $grand_total,
        'offline' => true,
        'message' => 'Database unavailable: sale saved locally and will appear in sales history.'
    ]);
    exit;
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    @file_put_contents(__DIR__ . '/../logs/db_errors.log', date('Y-m-d H:i:s') . " - sale error: " . $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);

    $invoice_no = 'INV-OFFLINE-' . date('YmdHis');
    $sale_date = date('Y-m-d H:i:s');
    $discount = ($subtotal * $discount_percent) / 100.0;
    $saleRecord = save_local_sale_record([
        'invoice_no' => $invoice_no,
        'customer_name' => $customer_name,
        'customer_phone' => '',
        'pharmacist_id' => $pharmacist_id,
        'pharmacist_name' => $pharmacist_name,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'grand_total' => $grand_total,
        'payment_method' => $payment_method,
        'sale_date' => $sale_date,
        'items' => $items
    ]);

    echo json_encode([
        'success' => true,
        'invoice_no' => $invoice_no,
        'sale_date' => $sale_date,
        'grand_total' => $grand_total,
        'offline' => true,
        'message' => 'Sale fell back to local storage because the database is unavailable.'
    ]);
    exit;
}
