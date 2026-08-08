<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$active_tab = $_GET['tab'] ?? 'sales';
$filter_period = $_GET['period'] ?? 'week';
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-chart-pie" style="color:var(--primary);"></i> Analytics & Reports Engine</h1>
                <p>System analysis metrics, sales revenues, inventory valuation, and expiry alerts.</p>
            </div>
            <div class="page-actions">
                <button class="btn btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Report</button>
                <button class="btn btn-primary" onclick="exportReportDemo('PDF')"><i class="fa-solid fa-file-pdf"></i> Export PDF</button>
            </div>
        </div>

        <!-- Filter Controls Bar -->
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-body" style="padding:1rem 1.5rem;">
                <form method="GET" action="reports.php" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($active_tab); ?>">
                    
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <span style="font-weight:600; font-size:0.85rem;"><i class="fa-solid fa-filter"></i> Date Filter:</span>
                        <a href="reports.php?tab=<?php echo $active_tab; ?>&period=today" class="btn btn-sm <?php echo ($filter_period == 'today') ? 'btn-primary' : 'btn-outline'; ?>">Today</a>
                        <a href="reports.php?tab=<?php echo $active_tab; ?>&period=week" class="btn btn-sm <?php echo ($filter_period == 'week') ? 'btn-primary' : 'btn-outline'; ?>">This Week</a>
                        <a href="reports.php?tab=<?php echo $active_tab; ?>&period=month" class="btn btn-sm <?php echo ($filter_period == 'month') ? 'btn-primary' : 'btn-outline'; ?>">This Month</a>
                    </div>

                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <input type="date" class="form-control" style="font-size:0.8rem; padding:0.3rem 0.6rem; width:140px;" value="2026-08-01">
                        <span style="font-size:0.8rem;">to</span>
                        <input type="date" class="form-control" style="font-size:0.8rem; padding:0.3rem 0.6rem; width:140px;" value="2026-08-08">
                        <button type="button" class="btn btn-sm btn-primary" onclick="triggerReportDemo('Custom Filtered Report')">Generate</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabbed Navigation Bar -->
        <div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; border-bottom:2px solid var(--border-color); padding-bottom:0.5rem;">
            <a href="reports.php?tab=sales&period=<?php echo $filter_period; ?>" class="btn btn-sm <?php echo ($active_tab == 'sales') ? 'btn-primary' : 'btn-outline'; ?>">
                <i class="fa-solid fa-sack-dollar"></i> Sales Report
            </a>
            <a href="reports.php?tab=purchases&period=<?php echo $filter_period; ?>" class="btn btn-sm <?php echo ($active_tab == 'purchases') ? 'btn-primary' : 'btn-outline'; ?>">
                <i class="fa-solid fa-truck-field"></i> Purchase Report
            </a>
            <a href="reports.php?tab=inventory&period=<?php echo $filter_period; ?>" class="btn btn-sm <?php echo ($active_tab == 'inventory') ? 'btn-primary' : 'btn-outline'; ?>">
                <i class="fa-solid fa-boxes-stacked"></i> Inventory Valuation
            </a>
            <a href="reports.php?tab=expiry&period=<?php echo $filter_period; ?>" class="btn btn-sm <?php echo ($active_tab == 'expiry') ? 'btn-primary' : 'btn-outline'; ?>">
                <i class="fa-solid fa-calendar-xmark"></i> Expiry Audit
            </a>
            <a href="reports.php?tab=low_stock&period=<?php echo $filter_period; ?>" class="btn btn-sm <?php echo ($active_tab == 'low_stock') ? 'btn-primary' : 'btn-outline'; ?>">
                <i class="fa-solid fa-triangle-exclamation"></i> Low Stock Report
            </a>
        </div>

        <!-- Dynamic Tab Content -->
        <?php if ($active_tab == 'sales'): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> Revenue & Sales Performance Chart
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="reportChart" height="120"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-table"></i> Sales Transaction Summary Breakdown</div>
                    <button class="btn btn-sm btn-outline" onclick="exportReportDemo('CSV')">Export CSV</button>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="table">
                        <thead>
                            <tr><th>Period</th><th>Invoices Issued</th><th>Units Sold</th><th>Gross Sales</th><th>Discounts</th><th>Net Revenue</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Aug 08, 2026</td><td>14 Invoices</td><td>145 Units</td><td>$585.00</td><td>$34.50</td><td><strong>$550.50</strong></td></tr>
                            <tr><td>Aug 07, 2026</td><td>22 Invoices</td><td>210 Units</td><td>$840.00</td><td>$60.00</td><td><strong>$780.00</strong></td></tr>
                            <tr><td>Aug 06, 2026</td><td>18 Invoices</td><td>180 Units</td><td>$560.00</td><td>$40.00</td><td><strong>$520.00</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_tab == 'purchases'): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-truck-ramp-box"></i> Vendor Purchase Orders Report</div>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="table">
                        <thead>
                            <tr><th>PO #</th><th>Vendor Supplier</th><th>Order Date</th><th>Units Procured</th><th>Total Expense</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><code>PO-2026-101</code></td><td>Square Pharmaceuticals Ltd.</td><td>2026-08-01</td><td>450 Units</td><td>$495.00</td><td><span class="badge badge-in-stock">Received</span></td></tr>
                            <tr><td><code>PO-2026-102</code></td><td>Beximco Pharmaceuticals Ltd.</td><td>2026-08-03</td><td>120 Units</td><td>$3,360.00</td><td><span class="badge badge-in-stock">Received</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_tab == 'expiry'): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-calendar-xmark" style="color:var(--purple);"></i> Near-Expiry & Expired Inventory Report</div>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="table">
                        <thead>
                            <tr><th>Medicine Name</th><th>Batch #</th><th>Expiry Date</th><th>Stock Qty</th><th>Disposal Status</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>Ceevit 250mg Chewable</strong></td><td><code>SQ-CV-102</code></td><td>2025-01-10</td><td>300 Units</td><td><span class="badge badge-expired">Expired - Quarantine</span></td></tr>
                            <tr><td><strong>Avolac Syrup 100ml</strong></td><td><code>IN-AV2023-99</code></td><td>2026-05-10</td><td>4 Units</td><td><span class="badge badge-warning">Expires in 90 Days</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-boxes-stacked"></i> Comprehensive Inventory Audit</div>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="table">
                        <thead>
                            <tr><th>Category</th><th>Total SKUs</th><th>In Stock SKUs</th><th>Total Valuation ($)</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Analgesics / Antipyretics</td><td>2 Items</td><td>1 Item</td><td>$1,215.00</td></tr>
                            <tr><td>Gastric / Anti-ulcerants</td><td>2 Items</td><td>1 Item</td><td>$72.00</td></tr>
                            <tr><td>Antibiotics</td><td>1 Item</td><td>1 Item</td><td>$4,200.00</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('reportChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Aug 02', 'Aug 03', 'Aug 04', 'Aug 05', 'Aug 06', 'Aug 07', 'Aug 08'],
                datasets: [{
                    label: 'Net Revenue ($)',
                    data: [420, 680, 510, 730, 520, 780, 550],
                    backgroundColor: '#0d9488',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
