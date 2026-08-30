<?php
$tab = $_GET['tab'] ?? 'sales';
$period = $_GET['period'] ?? 'week';
$path_prefix = '../';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/validators.php';
$tab = validate_tab($tab);
$period = validate_period($period);
$pdo = getDBConnection();

doctitle:
?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pharma Report - <?php echo htmlspecialchars(ucfirst($tab)); ?></title>
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>css/style.css">
    <style>
        body { padding:20px; font-family: Arial, Helvetica, sans-serif; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .title { font-size:20px; font-weight:700; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th,td { border:1px solid #ddd; padding:8px; font-size:13px; }
        th { background:#f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Pharma Management — <?php echo htmlspecialchars(ucfirst($tab)); ?> Report</div>
            <div style="font-size:12px; color:#444;">Period: <?php echo htmlspecialchars($period); ?> &nbsp;|&nbsp; Generated: <?php echo date('Y-m-d H:i:s'); ?></div>
        </div>
        <div><img src="<?php echo $path_prefix; ?>css/logo.png" alt="logo" style="height:40px; opacity:0.85;"></div>
    </div>

    <?php if ($tab === 'sales'): ?>
        <table>
            <thead><tr><th>Invoice No</th><th>Sale Date</th><th>Items</th><th>Grand Total</th></tr></thead>
            <tbody>
            <?php
            if ($pdo) {
                $stmt = $pdo->query("SELECT invoice_no, sale_date, items_count, grand_total FROM sales ORDER BY sale_date DESC LIMIT 1000");
                foreach ($stmt->fetchAll() as $r) {
                    echo '<tr><td>'.htmlspecialchars($r['invoice_no']).'</td><td>'.htmlspecialchars($r['sale_date']).'</td><td>'.htmlspecialchars($r['items_count']).'</td><td>'.htmlspecialchars($r['grand_total']).'</td></tr>';
                }
            } else {
                echo '<tr><td>INV-2026-001</td><td>2026-08-08 10:12:00</td><td>12</td><td>550.50</td></tr>';
                echo '<tr><td>INV-2026-002</td><td>2026-08-07 14:05:00</td><td>22</td><td>780.00</td></tr>';
            }
            ?>
            </tbody>
        </table>

    <?php elseif ($tab === 'purchases'): ?>
        <table>
            <thead><tr><th>PO No</th><th>Purchase Date</th><th>Supplier</th><th>Items</th><th>Grand Total</th></tr></thead>
            <tbody>
            <?php
            if ($pdo) {
                $stmt = $pdo->query("SELECT reference_no, purchase_date, supplier_name, items_count, grand_total FROM purchases ORDER BY purchase_date DESC LIMIT 1000");
                foreach ($stmt->fetchAll() as $r) {
                    echo '<tr><td>'.htmlspecialchars($r['reference_no']).'</td><td>'.htmlspecialchars($r['purchase_date']).'</td><td>'.htmlspecialchars($r['supplier_name']).'</td><td>'.htmlspecialchars($r['items_count']).'</td><td>'.htmlspecialchars($r['grand_total']).'</td></tr>';
                }
            } else {
                echo '<tr><td>PO-2026-101</td><td>2026-08-01</td><td>Square Pharmaceuticals</td><td>450</td><td>495.00</td></tr>';
                echo '<tr><td>PO-2026-102</td><td>2026-08-03</td><td>Beximco Pharmaceuticals</td><td>120</td><td>3360.00</td></tr>';
            }
            ?>
            </tbody>
        </table>

    <?php elseif ($tab === 'inventory'): ?>
        <table>
            <thead><tr><th>Category</th><th>SKU Count</th><th>Stock Qty</th><th>Valuation</th></tr></thead>
            <tbody>
            <?php
            if ($pdo) {
                $stmt = $pdo->query("SELECT c.category_name, COUNT(m.id) as skus, SUM(m.stock_quantity) as stock_qty, SUM(m.stock_quantity * m.selling_price) as valuation FROM medicines m LEFT JOIN categories c ON m.category_id = c.id GROUP BY m.category_id ORDER BY valuation DESC");
                foreach ($stmt->fetchAll() as $r) {
                    echo '<tr><td>'.htmlspecialchars($r['category_name'] ?: 'Uncategorized').'</td><td>'.htmlspecialchars($r['skus']).'</td><td>'.htmlspecialchars($r['stock_qty']).'</td><td>'.htmlspecialchars(number_format($r['valuation'],2)).'</td></tr>';
                }
            } else {
                echo '<tr><td>Analgesics</td><td>12</td><td>420</td><td>1215.00</td></tr>';
                echo '<tr><td>Gastric</td><td>8</td><td>72</td><td>72.00</td></tr>';
            }
            ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>Unsupported report type.</p>
    <?php endif; ?>

<script>
// Auto-print when opened for PDF export
window.addEventListener('DOMContentLoaded', function(){
    setTimeout(() => { window.print(); }, 400);
});
</script>
</body>
</html>
