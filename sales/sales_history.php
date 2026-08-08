<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sales = get_all_sales();
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-file-invoice-dollar" style="color:var(--primary);"></i> Sales Transaction History</h1>
                <p>Browse completed sales invoices, revenue logs, and printable customer receipts.</p>
            </div>
            <div class="page-actions">
                <a href="new_sale.php" class="btn btn-primary">
                    <i class="fa-solid fa-cash-register"></i> New Sale (POS)
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-list-ul"></i> Completed Invoices Log
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <input type="text" id="salesSearchInput" class="form-control" style="width:240px; padding:0.4rem 0.8rem;" placeholder="Search invoice or customer..." onkeyup="filterTable('salesSearchInput', 'salesTable')">
                    <button class="btn btn-sm btn-outline" onclick="triggerReportDemo('Sales Log Export')"><i class="fa-solid fa-file-excel"></i> Export Log</button>
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table" id="salesTable">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Pharmacist</th>
                                <th>Payment Method</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($sale['invoice_no']); ?></code></td>
                                <td><?php echo $sale['sale_date']; ?></td>
                                <td><strong><?php echo htmlspecialchars($sale['customer_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sale['pharmacist_name']); ?></td>
                                <td><span class="badge" style="background:#e0f2fe; color:#0369a1;"><?php echo htmlspecialchars($sale['payment_method']); ?></span></td>
                                <td><strong style="color:var(--primary-dark); font-size:0.95rem;">$<?php echo number_format($sale['grand_total'], 2); ?></strong></td>
                                <td><span class="badge badge-in-stock"><?php echo htmlspecialchars($sale['payment_status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline" onclick="viewHistoricalInvoice('<?php echo htmlspecialchars($sale['invoice_no']); ?>', '<?php echo htmlspecialchars($sale['customer_name']); ?>', '<?php echo number_format($sale['grand_total'], 2); ?>', '<?php echo htmlspecialchars($sale['payment_method']); ?>', '<?php echo $sale['sale_date']; ?>')">
                                        <i class="fa-solid fa-receipt"></i> View Receipt
                                    </button>
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
function viewHistoricalInvoice(invNo, customer, total, payment, date) {
    const html = `
        <div class="thermal-receipt">
            <div class="receipt-header">
                <h3>PharmaCare Pro</h3>
                <p>University Medical Complex</p>
                <hr>
                <p><strong>INVOICE: ${invNo}</strong></p>
                <p>Date: ${date}</p>
                <p>Customer: ${customer}</p>
                <p>Payment Method: ${payment}</p>
            </div>
            <table class="receipt-table">
                <thead>
                    <tr><th>Item Sample</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <tr><td>Napa 500mg x20</td><td>$30.00</td></tr>
                    <tr><td>Avolac Syrup 100ml x1</td><td>$140.00</td></tr>
                </tbody>
            </table>
            <hr>
            <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:13px;">
                <span>TOTAL PAID:</span><span>$${total}</span>
            </div>
            <div class="receipt-footer">
                <p>Thank you for choosing PharmaCare!</p>
            </div>
        </div>
    `;

    openModal(
        `Invoice Receipt - ${invNo}`,
        html,
        `<button class="btn btn-secondary" onclick="closeModal()">Close</button>
         <button class="btn btn-primary" onclick="window.print(); closeModal();"><i class="fa-solid fa-print"></i> Print Receipt</button>`
    );
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
