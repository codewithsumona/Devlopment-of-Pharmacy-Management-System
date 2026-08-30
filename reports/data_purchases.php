<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/validators.php';
$pdo = getDBConnection();

$period = validate_period($_GET['period'] ?? null);

if ($pdo) {
    try {
        if ($period === 'today') {
            $stmt = $pdo->prepare("SELECT HOUR(purchase_date) as hr, SUM(grand_total) as total FROM purchases WHERE DATE(purchase_date) = CURDATE() GROUP BY hr ORDER BY hr ASC");
            $stmt->execute();
            $hourMap = [];
            foreach ($stmt->fetchAll() as $r) $hourMap[(int)$r['hr']] = (float)$r['total'];
            $labels = [];$data = [];
            for ($h=0;$h<24;$h++) { $labels[] = sprintf('%02d:00',$h); $data[] = isset($hourMap[$h]) ? $hourMap[$h] : 0; }
        } elseif ($period === 'month') {
            $stmt = $pdo->prepare("SELECT DATE(purchase_date) as d, SUM(grand_total) as total FROM purchases WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(purchase_date) ORDER BY DATE(purchase_date) ASC");
            $stmt->execute();
            $map = [];
            foreach ($stmt->fetchAll() as $r) $map[$r['d']] = (float)$r['total'];
            $labels=[]; $data=[];
            for ($i=29;$i>=0;$i--) { $d = date('Y-m-d', strtotime("-{$i} days")); $labels[] = date('M d', strtotime($d)); $data[] = isset($map[$d]) ? $map[$d] : 0; }
        } else {
            $stmt = $pdo->prepare("SELECT DATE(purchase_date) as d, SUM(grand_total) as total FROM purchases WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(purchase_date) ORDER BY DATE(purchase_date) ASC");
            $stmt->execute();
            $map = [];
            foreach ($stmt->fetchAll() as $r) $map[$r['d']] = (float)$r['total'];
            $labels=[]; $data=[];
            for ($i=6;$i>=0;$i--) { $d = date('Y-m-d', strtotime("-{$i} days")); $labels[] = date('D M d', strtotime($d)); $data[] = isset($map[$d]) ? $map[$d] : 0; }
        }
        echo json_encode(['success'=>true,'labels'=>$labels,'data'=>$data]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'DB error: '.$e->getMessage()]);
        exit;
    }
}

// fallback sample
$labels = ['Aug 02','Aug 03','Aug 04','Aug 05','Aug 06','Aug 07','Aug 08'];
$data = [150, 420, 300, 520, 410, 620, 300];
echo json_encode(['success'=>true,'labels'=>$labels,'data'=>$data]);
exit;
