<?php
/**
 * Database Helper & Sample Data Engine
 * Mergen Pharmacy Management System
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Session default user role setup
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'Admin'; // Default role: Admin
    $_SESSION['user_name'] = 'Dr. Sarah Jenkins';
    $_SESSION['user_email'] = 'admin@pharma.com';
}

/**
 * Get sample static data fallback when DB is disconnected
 */
function get_fallback_medicines() {
    return [
        [
            'id' => 1,
            'medicine_name' => 'Napa 500mg',
            'generic_name' => 'Paracetamol',
            'category_name' => 'Analgesics / Antipyretics',
            'category_id' => 1,
            'manufacturer' => 'Square Pharmaceuticals',
            'batch_number' => 'SQ-N2024-01',
            'purchase_price' => 1.10,
            'selling_price' => 1.50,
            'stock_quantity' => 450,
            'expiry_date' => '2027-11-30',
            'supplier_name' => 'Square Pharmaceuticals Ltd.',
            'supplier_id' => 1,
            'status' => 'In Stock',
            'description' => 'Standard pain relief and antipyretic tablets.'
        ],
        [
            'id' => 2,
            'medicine_name' => 'Seclo 20mg',
            'generic_name' => 'Omeprazole',
            'category_name' => 'Gastric / Anti-ulcerants',
            'category_id' => 2,
            'manufacturer' => 'Square Pharmaceuticals',
            'batch_number' => 'SQ-S2024-88',
            'purchase_price' => 4.50,
            'selling_price' => 6.00,
            'stock_quantity' => 12,
            'expiry_date' => '2026-09-15',
            'supplier_name' => 'Square Pharmaceuticals Ltd.',
            'supplier_id' => 1,
            'status' => 'Low Stock',
            'description' => 'Proton pump inhibitor for gastric relief.'
        ],
        [
            'id' => 3,
            'medicine_name' => 'Fexo 120mg',
            'generic_name' => 'Fexofenadine Hydrochloride',
            'category_name' => 'Antihistamines',
            'category_id' => 3,
            'manufacturer' => 'Beximco Pharmaceuticals',
            'batch_number' => 'BX-F120-04',
            'purchase_price' => 7.00,
            'selling_price' => 9.50,
            'stock_quantity' => 85,
            'expiry_date' => '2026-12-01',
            'supplier_name' => 'Beximco Pharmaceuticals Ltd.',
            'supplier_id' => 3,
            'status' => 'In Stock',
            'description' => 'Non-drowsy antihistamine for allergy symptoms.'
        ],
        [
            'id' => 4,
            'medicine_name' => 'Avolac Syrup 100ml',
            'generic_name' => 'Lactulose',
            'category_name' => 'Laxatives / Syrup',
            'category_id' => 5,
            'manufacturer' => 'Incepta Pharmaceuticals',
            'batch_number' => 'IN-AV2023-99',
            'purchase_price' => 110.00,
            'selling_price' => 140.00,
            'stock_quantity' => 4,
            'expiry_date' => '2026-05-10',
            'supplier_name' => 'Incepta Pharmaceuticals Ltd.',
            'supplier_id' => 2,
            'status' => 'Low Stock',
            'description' => 'Osmotic laxative syrup for constipation management.'
        ],
        [
            'id' => 5,
            'medicine_name' => 'Omeprazole 20mg',
            'generic_name' => 'Omeprazole',
            'category_name' => 'Gastric / Anti-ulcerants',
            'category_id' => 2,
            'manufacturer' => 'Renata Limited',
            'batch_number' => 'RN-OMP-33',
            'purchase_price' => 3.80,
            'selling_price' => 5.00,
            'stock_quantity' => 0,
            'expiry_date' => '2026-10-20',
            'supplier_name' => 'Renata Limited',
            'supplier_id' => 4,
            'status' => 'Out of Stock',
            'description' => 'Generic gastric acid suppressor.'
        ],
        [
            'id' => 6,
            'medicine_name' => 'Azithrocin 500mg',
            'generic_name' => 'Azithromycin',
            'category_name' => 'Antibiotics',
            'category_id' => 4,
            'manufacturer' => 'Beximco Pharmaceuticals',
            'batch_number' => 'BX-AZ-771',
            'purchase_price' => 28.00,
            'selling_price' => 35.00,
            'stock_quantity' => 120,
            'expiry_date' => '2027-04-15',
            'supplier_name' => 'Beximco Pharmaceuticals Ltd.',
            'supplier_id' => 3,
            'status' => 'In Stock',
            'description' => 'Broad-spectrum macrolide antibiotic.'
        ],
        [
            'id' => 7,
            'medicine_name' => 'Ceevit 250mg Chewable',
            'generic_name' => 'Ascorbic Acid (Vitamin C)',
            'category_name' => 'Analgesics / Antipyretics',
            'category_id' => 1,
            'manufacturer' => 'Square Pharmaceuticals',
            'batch_number' => 'SQ-CV-102',
            'purchase_price' => 1.80,
            'selling_price' => 2.50,
            'stock_quantity' => 300,
            'expiry_date' => '2025-01-10',
            'supplier_name' => 'Square Pharmaceuticals Ltd.',
            'supplier_id' => 1,
            'status' => 'Expired',
            'description' => 'Chewable vitamin C supplement for immunity.'
        ]
    ];
}

function get_all_medicines() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("
                SELECT m.*, c.category_name, s.supplier_name 
                FROM medicines m 
                LEFT JOIN categories c ON m.category_id = c.id 
                LEFT JOIN suppliers s ON m.supplier_id = s.id 
                ORDER BY m.id ASC
            ");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {
            // Fallback
        }
    }
    return get_fallback_medicines();
}

function get_all_suppliers() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY id ASC");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {}
    }
    return [
        ['id' => 1, 'supplier_name' => 'Square Pharmaceuticals Ltd.', 'company_name' => 'Square Pharma', 'phone' => '+880 2-8833047', 'email' => 'info@squarepharma.com.bd', 'address' => 'Square Centre, 48 Mohakhali C/A, Dhaka', 'status' => 'Active'],
        ['id' => 2, 'supplier_name' => 'Incepta Pharmaceuticals Ltd.', 'company_name' => 'Incepta Pharma', 'phone' => '+880 2-8891688', 'email' => 'contact@inceptapharma.com', 'address' => '40 Shahid Tajuddin Sarani, Tejgaon, Dhaka', 'status' => 'Active'],
        ['id' => 3, 'supplier_name' => 'Beximco Pharmaceuticals Ltd.', 'company_name' => 'Beximco Pharma', 'phone' => '+880 2-58611001', 'email' => 'info@beximcopharma.com', 'address' => '19 Dhanmondi R/A, Road 7, Dhaka', 'status' => 'Active'],
        ['id' => 4, 'supplier_name' => 'Renata Limited', 'company_name' => 'Renata Ltd', 'phone' => '+880 2-8011013', 'email' => 'sales@renata-ltd.com', 'address' => 'Plot 1, Milk Vita Road, Section 7, Mirpur, Dhaka', 'status' => 'Active']
    ];
}

function get_all_staff() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {}
    }
    return [
        ['id' => 1, 'username' => 'admin', 'full_name' => 'Dr. Sarah Jenkins', 'email' => 'admin@pharma.com', 'phone' => '+880 1711-000111', 'role' => 'Admin', 'status' => 'Active'],
        ['id' => 2, 'username' => 'pharmacist', 'full_name' => 'Alex Rivera, PharmD', 'email' => 'alex@pharma.com', 'phone' => '+880 1819-222333', 'role' => 'Pharmacist', 'status' => 'Active'],
        ['id' => 3, 'username' => 'staff1', 'full_name' => 'David Miller', 'email' => 'david@pharma.com', 'phone' => '+880 1912-444555', 'role' => 'Staff', 'status' => 'Active']
    ];
}

function get_all_sales() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("
                SELECT s.*, u.full_name as pharmacist_name 
                FROM sales s 
                LEFT JOIN users u ON s.pharmacist_id = u.id 
                ORDER BY s.id DESC
            ");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {}
    }

    $logFile = __DIR__ . '/../logs/sales_log.json';
    if (file_exists($logFile)) {
        $raw = @file_get_contents($logFile);
        if ($raw !== false) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                $rows = [];
                foreach ($decoded as $sale) {
                    $rows[] = [
                        'id' => $sale['id'] ?? 0,
                        'invoice_no' => $sale['invoice_no'] ?? 'INV-LOCAL',
                        'customer_name' => $sale['customer_name'] ?? 'Walk-in Customer',
                        'customer_phone' => $sale['customer_phone'] ?? '',
                        'pharmacist_name' => $sale['pharmacist_name'] ?? 'System User',
                        'grand_total' => (float)($sale['grand_total'] ?? 0),
                        'payment_method' => $sale['payment_method'] ?? 'Cash',
                        'payment_status' => $sale['payment_status'] ?? 'Paid',
                        'sale_date' => $sale['sale_date'] ?? date('Y-m-d H:i:s')
                    ];
                }
                return $rows;
            }
        }
    }

    return [
        ['id' => 1, 'invoice_no' => 'INV-2026-001', 'customer_name' => 'Mr. Rahman', 'customer_phone' => '+880 1711-223344', 'pharmacist_name' => 'Alex Rivera, PharmD', 'grand_total' => 140.00, 'payment_method' => 'Cash', 'payment_status' => 'Paid', 'sale_date' => '2026-08-08 10:15:00'],
        ['id' => 2, 'invoice_no' => 'INV-2026-002', 'customer_name' => 'Walk-in Customer', 'customer_phone' => 'N/A', 'pharmacist_name' => 'Alex Rivera, PharmD', 'grand_total' => 85.50, 'payment_method' => 'Card', 'payment_status' => 'Paid', 'sale_date' => '2026-08-08 11:40:00'],
        ['id' => 3, 'invoice_no' => 'INV-2026-003', 'customer_name' => 'Mrs. Jahan', 'customer_phone' => '+880 1819-998877', 'pharmacist_name' => 'Dr. Sarah Jenkins', 'grand_total' => 325.00, 'payment_method' => 'Mobile Banking (bKash)', 'payment_status' => 'Paid', 'sale_date' => '2026-08-08 13:20:00']
    ];
}

function get_all_purchases() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("
                SELECT p.*, s.company_name as supplier_name 
                FROM purchases p 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                ORDER BY p.id DESC
            ");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {}
    }
    return [
        ['id' => 1, 'purchase_no' => 'PO-2026-101', 'supplier_name' => 'Square Pharma', 'purchase_date' => '2026-08-01', 'total_items' => 450, 'total_amount' => 495.00, 'status' => 'Received'],
        ['id' => 2, 'purchase_no' => 'PO-2026-102', 'supplier_name' => 'Beximco Pharma', 'purchase_date' => '2026-08-03', 'total_items' => 120, 'total_amount' => 3360.00, 'status' => 'Received'],
        ['id' => 3, 'purchase_no' => 'PO-2026-103', 'supplier_name' => 'Incepta Pharma', 'purchase_date' => '2026-08-07', 'total_items' => 10, 'total_amount' => 1100.00, 'status' => 'Pending']
    ];
}

function get_dashboard_kpi() {
    $meds = get_all_medicines();
    $sales = get_all_sales();
    $purchases = get_all_purchases();
    $suppliers = get_all_suppliers();
    $staff = get_all_staff();

    $total_meds = count($meds);
    $low_stock = 0;
    $expired = 0;

    foreach ($meds as $m) {
        if ($m['status'] === 'Low Stock' || ($m['stock_quantity'] > 0 && $m['stock_quantity'] <= 15)) {
            $low_stock++;
        }
        if ($m['status'] === 'Expired') {
            $expired++;
        }
    }

    $total_sales_val = 0;
    foreach ($sales as $s) {
        $total_sales_val += $s['grand_total'];
    }

    $total_purchases_val = 0;
    foreach ($purchases as $p) {
        $total_purchases_val += $p['total_amount'];
    }

    return [
        'total_medicines' => $total_meds,
        'low_stock' => $low_stock,
        'expired' => $expired,
        'todays_sales' => '$' . number_format(550.50, 2),
        'total_sales' => '$' . number_format($total_sales_val, 2),
        'total_purchases' => '$' . number_format($total_purchases_val, 2),
        'total_suppliers' => count($suppliers),
        'total_staff' => count($staff)
    ];
}
?>
