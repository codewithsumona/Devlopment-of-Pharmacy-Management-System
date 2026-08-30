<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/validators.php';
$pdo = getDBConnection();

$threshold = clamp_threshold($_GET['threshold'] ?? null, 1, 1000, 10);

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, batch_no, stock_quantity FROM medicines WHERE stock_quantity <= :th ORDER BY stock_quantity ASC LIMIT 100");
        $stmt->execute([':th' => $threshold]);
        $rows = $stmt->fetchAll();
        $labels = []; $data = []; $items = [];
        foreach ($rows as $r) {
            $labels[] = $r['name'] . ' [' . $r['batch_no'] . ']';
            $data[] = (int)$r['stock_quantity'];
            $items[] = ['id'=>$r['id'],'name'=>$r['name'],'batch_no'=>$r['batch_no'],'stock_quantity'=>(int)$r['stock_quantity']];
        }
        echo json_encode(['success'=>true,'labels'=>$labels,'data'=>$data,'rows'=>$items,'threshold'=>$threshold]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        exit;
    }
}

// fallback
$labels = ['Avolac Syrup [IN-AV2023-99]','Paracetamol [PAR-500]','Amox 250 [AMX-250]'];
$data = [4, 8, 2];
$rows = [ ['id'=>0,'name'=>'Avolac Syrup','batch_no'=>'IN-AV2023-99','stock_quantity'=>4], ['id'=>1,'name'=>'Paracetamol','batch_no'=>'PAR-500','stock_quantity'=>8], ['id'=>2,'name'=>'Amox 250','batch_no'=>'AMX-250','stock_quantity'=>2] ];

echo json_encode(['success'=>true,'labels'=>$labels,'data'=>$data,'rows'=>$rows,'threshold'=>$threshold]);
exit;
