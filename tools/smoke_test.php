<?php
// Simple smoke test script for local XAMPP deployment
// Usage: php tools/smoke_test.php http://localhost/pharmacy_management_prototype
$base = $argv[1] ?? 'http://localhost/pharmacy_management_prototype';
$endpoints = [
    '/',
    '/index.php',
    '/login.php',
    '/medicines/medicine_list.php',
    '/medicines/add_medicine.php',
    '/staff/staff_list.php',
    '/suppliers/supplier_list.php',
    '/purchases/add_purchase.php',
    '/reports/reports.php',
    '/reports/data_sales.php?period=week',
    '/reports/data_purchases.php?period=week',
    '/reports/data_inventory.php',
    '/reports/data_expiry.php',
    '/reports/data_low_stock.php?threshold=10',
    '/reports/export.php?tab=sales&format=csv',
    '/reports/print.php?tab=sales'
];

echo "Smoke test base URL: $base\n\n";
$results = [];
foreach ($endpoints as $e) {
    $url = rtrim($base, '/') . $e;
    $ok = false;
    $err = '';
    $status = 0;
    try {
        $headers = @get_headers($url);
        if ($headers && is_array($headers)) {
            // parse status code from first header
            if (preg_match('#HTTP/\d\.\d\s+(\d{3})#', $headers[0], $m)) $status = (int)$m[1];
            $ok = ($status >= 200 && $status < 400);
        } else {
            $err = 'No response (server down or URL unreachable)';
        }
    } catch (Exception $ex) {
        $err = $ex->getMessage();
    }
    $results[] = ['url'=>$url,'status'=>$status,'ok'=>$ok,'error'=>$err];
}

foreach ($results as $r) {
    $pad = str_repeat(' ', max(1, 60 - strlen($r['url'])));
    echo $r['url'] . $pad . ' ' . ($r['ok'] ? "OK ({$r['status']})" : "FAIL ({$r['status']})") . ($r['error'] ? ' - '.$r['error'] : '') . "\n";
}

echo "\nNote: this script issues simple HTTP requests and does not perform authenticated or destructive operations. Start Apache (XAMPP) and ensure `pharmacy_management_prototype` is in your webroot before running.\n";
