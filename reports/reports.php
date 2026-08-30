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
                <div class="card-body">
                    <canvas id="purchaseChart" height="120" style="margin-bottom:1rem;"></canvas>
                </div>

                <div class="card">
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
            </div>

        <?php elseif ($active_tab == 'inventory'): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-boxes-stacked" style="color:var(--primary);"></i> Inventory Valuation by Category</div>
                    <button class="btn btn-sm btn-outline" onclick="exportReportDemo('CSV')">Export CSV</button>
                </div>
                <div class="card-body">
                    <canvas id="inventoryChart" height="140"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title"><i class="fa-solid fa-table"></i> Inventory Summary</div></div>
                <div class="card-body" style="padding:0;">
                    <table class="table">
                        <thead><tr><th>Category</th><th>SKUs</th><th>Stock Qty</th><th>Valuation ($)</th></tr></thead>
                        <tbody>
                            <tr><td>Analgesics</td><td>12</td><td>420</td><td>$1,215.00</td></tr>
                            <tr><td>Gastric</td><td>8</td><td>72</td><td>$72.00</td></tr>
                            <tr><td>Antibiotics</td><td>6</td><td>140</td><td>$4,200.00</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($active_tab == 'expiry'): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-calendar-xmark" style="color:var(--purple);"></i> Near-Expiry & Expired Inventory Report</div>
                    <button class="btn btn-sm btn-outline" onclick="exportReportDemo('CSV')">Export CSV</button>
                </div>
                <div class="card-body">
                    <canvas id="expiryChart" height="120" style="margin-bottom:1rem;"></canvas>
                </div>

                <div class="card">
                    <div class="card-body" style="padding:0;">
                        <table class="table" id="expiryTable">
                            <thead>
                                <tr><th>Medicine Name</th><th>Batch #</th><th>Expiry Date</th><th>Stock Qty</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="5">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
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
        // Fetch sales data from API
        fetch('data_sales.php?period=<?php echo $filter_period; ?>')
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error(json.message || 'No data');
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: json.labels,
                        datasets: [{
                            label: 'Net Revenue ($)',
                            data: json.data,
                            backgroundColor: '#0d9488',
                            borderRadius: 6
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            })
            .catch(err => {
                console.error('Report data error', err);
            });
    }
    // Inventory chart
    const invCtx = document.getElementById('inventoryChart');
    if (invCtx) {
        fetch('data_inventory.php')
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error(json.message || 'No data');
                new Chart(invCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: { labels: json.labels, datasets: [{ data: json.data, backgroundColor: ['#0ea5a4','#f97316','#06b6d4','#a78bfa'] }] },
                    options: { responsive: true, plugins: { legend: { position: 'right' } } }
                });
            })
            .catch(err => console.error('Inventory data error', err));
    }

    // Purchases chart
    const purchaseCtx = document.getElementById('purchaseChart');
    if (purchaseCtx) {
        fetch('data_purchases.php?period=<?php echo $filter_period; ?>')
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error(json.message || 'No data');
                new Chart(purchaseCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: json.labels,
                        datasets: [{ label: 'Purchases ($)', data: json.data, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.2 }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            })
            .catch(err => console.error('Purchases data error', err));
    }

    // Expiry chart + table
    const expiryCtx = document.getElementById('expiryChart');
    const expiryTable = document.getElementById('expiryTable');
    if (expiryCtx) {
        fetch('data_expiry.php')
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error(json.message || 'No data');
                new Chart(expiryCtx.getContext('2d'), {
                    type: 'bar',
                    data: { labels: json.labels, datasets: [{ label: 'Count', data: json.data, backgroundColor: ['#ef4444','#f59e0b','#10b981'] }] },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });

                // populate table
                if (expiryTable && json.rows) {
                    const tbody = expiryTable.querySelector('tbody');
                    tbody.innerHTML = '';
                    json.rows.forEach(r => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${escapeHtml(r.name)}</td><td><code>${escapeHtml(r.batch_no)}</code></td><td>${escapeHtml(r.expiry_date)}</td><td>${escapeHtml(r.stock_quantity)}</td><td>${escapeHtml(r.status)}</td>`;
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(err => console.error('Expiry data error', err));
    }

    // Low-stock chart + table
    const lowCtx = document.getElementById('lowStockChart');
    const lowTable = document.getElementById('lowStockTable');
    if (lowCtx) {
        fetch('data_low_stock.php?threshold=10')
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error(json.message || 'No data');
                new Chart(lowCtx.getContext('2d'), {
                    type: 'bar',
                    data: { labels: json.labels, datasets: [{ label: 'Stock Qty', data: json.data, backgroundColor: '#f97316' }] },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
                if (lowTable && json.rows) {
                    const tbody = lowTable.querySelector('tbody');
                    tbody.innerHTML = '';
                    json.rows.forEach(r => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${escapeHtml(r.name)}</td><td><code>${escapeHtml(r.batch_no)}</code></td><td>${escapeHtml(r.stock_quantity)}</td>`;
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(err => console.error('Low stock data error', err));
    }

    // small util for escaping
    function escapeHtml(s) { return (s === null || s === undefined) ? '' : String(s).replace(/[&"'<>]/g, function (c) { return {'&':'&amp;','"':'&quot;',"'":"&#39;",'<':'&lt;','>':'&gt;'}[c]; }); }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
