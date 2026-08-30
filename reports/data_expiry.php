<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/validators.php';
$pdo = getDBConnection();

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, name, batch_no, expiry_date, stock_quantity FROM medicines WHERE expiry_date IS NOT NULL ORDER BY expiry_date ASC");
        $rows = $stmt->fetchAll();
        $expired = 0; $near = 0; $safe = 0; $list = [];
        foreach ($rows as $r) {
            $d = strtotime($r['expiry_date']);
            $diff = ($d - strtotime(date('Y-m-d'))) / (60*60*24);
            $status = 'OK';
            if ($diff < 0) { $expired++; $status = 'Expired'; }
            elseif ($diff <= 90) { $near++; $status = 'Near Expiry'; }
            else { $safe++; }
            $list[] = [ 'id' => $r['id'], 'name' => $r['name'], 'batch_no' => $r['batch_no'], 'expiry_date' => $r['expiry_date'], 'stock_quantity' => (int)$r['stock_quantity'], 'status' => $status, 'days_to_expiry' => (int)$diff ];
        }
        echo json_encode(['success'=>true,'labels'=>['Expired','Near Expiry','Safe'],'data'=>[$expired,$near,$safe],'rows'=>$list]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        exit;
    }
}

// fallback sample
$labels = ['Expired','Near Expiry','Safe'];
$data = [2, 5, 23];
$rows = [
    ['id'=>0,'name'=>'Ceevit 250mg Chewable','batch_no'=>'SQ-CV-102','expiry_date'=>'2025-01-10','stock_quantity'=>300,'status'=>'Expired','days_to_expiry'=>-600],
    ['id'=>1,'name'=>'Avolac Syrup 100ml','batch_no'=>'IN-AV2023-99','expiry_date'=>'2026-05-10','stock_quantity'=>4,'status'=>'Near Expiry','days_to_expiry'=>280]
];

echo json_encode(['success'=>true,'labels'=>$labels,'data'=>$data,'rows'=>$rows]);
exit;
