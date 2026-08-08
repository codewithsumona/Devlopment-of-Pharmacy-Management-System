<?php
$path_prefix = '';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$kpi = get_dashboard_kpi();
$medicines = get_all_medicines();
$sales = get_all_sales();
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="content-body">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-gauge-high" style="color: var(--primary);"></i> Dashboard Overview</h1>
                <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong> (<?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Admin'); ?> View)</p>
            </div>
            <div class="page-actions">
                <a href="sales/new_sale.php" class="btn btn-primary">
                    <i class="fa-solid fa-cart-plus"></i> New POS Sale
                </a>
                <a href="medicines/add_medicine.php" class="btn btn-outline">
                    <i class="fa-solid fa-plus"></i> Add Medicine
                </a>
            </div>
        </div>

        <!-- 8 Key Metric Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fa-solid fa-pills"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['total_medicines']; ?></h3>
                    <p>Total Medicines</p>
                    <div class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> Active Catalog</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon amber">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['low_stock']; ?></h3>
                    <p>Low Stock Items</p>
                    <div class="stat-trend down"><i class="fa-solid fa-arrow-trend-down"></i> Action Needed</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['expired']; ?></h3>
                    <p>Expired Medicines</p>
                    <div class="stat-trend down"><i class="fa-solid fa-ban"></i> Quarantine</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon emerald">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['todays_sales']; ?></h3>
                    <p>Today's Revenue</p>
                    <div class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> +14.2% Today</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['total_sales']; ?></h3>
                    <p>Total Sales Revenue</p>
                    <div class="stat-trend up"><i class="fa-solid fa-chart-line"></i> Cumulative</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon rose">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['total_purchases']; ?></h3>
                    <p>Total Purchases</p>
                    <div class="stat-trend up"><i class="fa-solid fa-cart-shopping"></i> Stock Inflow</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fa-solid fa-building-user"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['total_suppliers']; ?></h3>
                    <p>Active Suppliers</p>
                    <div class="stat-trend up"><i class="fa-solid fa-check"></i> Verified Vendors</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $kpi['total_staff']; ?></h3>
                    <p>Pharmacy Staff</p>
                    <div class="stat-trend up"><i class="fa-solid fa-user-check"></i> Active Roster</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-area" style="color:var(--primary);"></i> Sales Revenue & Performance Overview
                    </div>
                    <span class="badge badge-in-stock">Weekly Trend</span>
                </div>
                <div class="card-body">
                    <canvas id="salesOverviewChart" height="220"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-pie" style="color:var(--secondary);"></i> Inventory Stock Distribution
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="stockDistributionChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <!-- Two Column Tables Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Low Stock Alerts -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-triangle-exclamation" style="color:var(--warning);"></i> Low-Stock Medicine Alerts
                    </div>
                    <a href="inventory/inventory.php" class="btn btn-sm btn-outline">View Inventory</a>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Category</th>
                                    <th>Qty Left</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $low_items = array_filter($medicines, function($m) {
                                    return $m['status'] === 'Low Stock' || $m['status'] === 'Out of Stock' || $m['stock_quantity'] <= 15;
                                });
                                foreach (array_slice($low_items, 0, 4) as $item): 
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['medicine_name']); ?></strong>
                                        <div style="font-size:0.75rem; color:#64748b;"><?php echo htmlspecialchars($item['generic_name']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                    <td><strong style="color:var(--danger);"><?php echo $item['stock_quantity']; ?> units</strong></td>
                                    <td>
                                        <?php if ($item['status'] === 'Out of Stock'): ?>
                                            <span class="badge badge-out-stock">Out of Stock</span>
                                        <?php else: ?>
                                            <span class="badge badge-low-stock">Low Stock</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Sales Log -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-receipt" style="color:var(--success);"></i> Recent Sales Transactions
                    </div>
                    <a href="sales/sales_history.php" class="btn btn-sm btn-outline">View History</a>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($sales, 0, 4) as $sale): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($sale['invoice_no']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                    <td><strong>$<?php echo number_format($sale['grand_total'], 2); ?></strong></td>
                                    <td><span class="badge badge-in-stock"><?php echo htmlspecialchars($sale['payment_status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recently Added Medicines List -->
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);"></i> Recently Cataloged Medicines
                </div>
                <a href="medicines/medicine_list.php" class="btn btn-sm btn-primary">Full Medicine List</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Medicine Name</th>
                                <th>Generic Name</th>
                                <th>Manufacturer</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($medicines, 0, 5) as $med): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($med['batch_number']); ?></code></td>
                                <td><strong><?php echo htmlspecialchars($med['medicine_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($med['generic_name']); ?></td>
                                <td><?php echo htmlspecialchars($med['manufacturer']); ?></td>
                                <td>$<?php echo number_format($med['selling_price'], 2); ?></td>
                                <td><?php echo $med['stock_quantity']; ?></td>
                                <td><?php echo $med['expiry_date']; ?></td>
                                <td>
                                    <?php 
                                    if ($med['status'] == 'In Stock') echo '<span class="badge badge-in-stock">In Stock</span>';
                                    else if ($med['status'] == 'Low Stock') echo '<span class="badge badge-low-stock">Low Stock</span>';
                                    else if ($med['status'] == 'Out of Stock') echo '<span class="badge badge-out-stock">Out of Stock</span>';
                                    else echo '<span class="badge badge-expired">Expired</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Sales Overview Chart
    const salesCtx = document.getElementById('salesOverviewChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales ($)',
                data: [320, 450, 280, 610, 520, 780, 550],
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13, 148, 136, 0.12)',
                tension: 0.3,
                fill: true,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // 2. Stock Distribution Chart
    const stockCtx = document.getElementById('stockDistributionChart').getContext('2d');
    new Chart(stockCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Expired'],
            datasets: [{
                data: [4, 2, 1, 1],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
