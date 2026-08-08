<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$medicines = get_all_medicines();
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header" style="margin-bottom:1rem;">
            <div class="page-title">
                <h1><i class="fa-solid fa-cash-register" style="color:var(--primary);"></i> Pharmacy POS Register</h1>
                <p>Create new sales transaction, search medications, and issue printable receipts.</p>
            </div>
            <div class="page-actions">
                <a href="sales_history.php" class="btn btn-outline">
                    <i class="fa-solid fa-clock-rotate-left"></i> Sales History
                </a>
            </div>
        </div>

        <div class="pos-container">
            <!-- Left Item Selection Catalog -->
            <div class="pos-catalog">
                <div class="card" style="margin-bottom:0;">
                    <div class="card-header" style="padding: 0.85rem 1.25rem;">
                        <div class="search-box" style="width:100%;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="posCatalogSearch" placeholder="Search medicine by name, generic, or batch..." onkeyup="filterPosItems()">
                        </div>
                    </div>
                </div>

                <div class="pos-items-grid" id="posItemsGrid">
                    <?php foreach ($medicines as $med): ?>
                    <div class="pos-item-card" data-name="<?php echo strtolower($med['medicine_name'] . ' ' . $med['generic_name']); ?>" onclick="addToPosCart(<?php echo $med['id']; ?>, '<?php echo addslashes($med['medicine_name']); ?>', <?php echo $med['selling_price']; ?>, <?php echo $med['stock_quantity']; ?>)">
                        <div>
                            <div class="pos-item-name"><?php echo htmlspecialchars($med['medicine_name']); ?></div>
                            <div class="pos-item-generic"><?php echo htmlspecialchars($med['generic_name']); ?></div>
                        </div>
                        <div class="pos-item-footer">
                            <div class="pos-item-price">$<?php echo number_format($med['selling_price'], 2); ?></div>
                            <?php if ($med['stock_quantity'] <= 0): ?>
                                <span class="badge badge-out-stock">Out</span>
                            <?php elseif ($med['stock_quantity'] <= 15): ?>
                                <span class="badge badge-low-stock"><?php echo $med['stock_quantity']; ?> left</span>
                            <?php else: ?>
                                <span class="badge badge-in-stock"><?php echo $med['stock_quantity']; ?> in stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right POS Register Cart -->
            <div class="pos-cart">
                <div class="pos-cart-header">
                    <span><i class="fa-solid fa-cart-shopping" style="color:var(--primary);"></i> Current Order Cart</span>
                    <button class="btn btn-sm btn-outline" style="color:var(--danger);" onclick="clearCart()">
                        <i class="fa-solid fa-trash-can"></i> Clear
                    </button>
                </div>

                <!-- Customer & Payment Setup Bar -->
                <div style="padding:0.75rem 1.25rem; background:#f8fafc; border-bottom:1px solid var(--border-color); display:flex; gap:0.5rem;">
                    <input type="text" id="posCustomerName" class="form-control" style="font-size:0.8rem; padding:0.35rem 0.6rem;" placeholder="Customer Name (Optional)">
                    <select id="posPaymentMethod" class="form-control" style="font-size:0.8rem; padding:0.35rem 0.6rem; width:130px;">
                        <option value="Cash">Cash</option>
                        <option value="Card">Credit Card</option>
                        <option value="Mobile Banking (bKash)">bKash / MFS</option>
                    </select>
                </div>

                <!-- Dynamic Cart Items Table -->
                <div class="pos-cart-items" id="posCartItemsContainer">
                    <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
                        <i class="fa-solid fa-cart-shopping" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                        <p>No medicines selected for sale.</p>
                        <span style="font-size: 0.8rem;">Click items on the left catalog to add to sale.</span>
                    </div>
                </div>

                <!-- Cart Totals & Checkout Actions -->
                <div class="pos-cart-summary">
                    <div class="summary-row">
                        <span>Subtotal Amount:</span>
                        <strong id="posSubtotal">$0.00</strong>
                    </div>
                    <div class="summary-row" style="align-items:center;">
                        <span>Discount (%):</span>
                        <input type="number" id="posDiscountInput" class="form-control" style="width:70px; padding:0.2rem 0.5rem; font-size:0.85rem; text-align:right;" value="0" min="0" max="100" oninput="renderPosCart()">
                    </div>
                    <div class="summary-row total">
                        <span>Grand Total:</span>
                        <span id="posGrandTotal" style="color:var(--primary);">$0.00</span>
                    </div>

                    <button class="btn btn-primary btn-lg" style="width:100%; margin-top:1rem;" onclick="processCompleteSale()">
                        <i class="fa-solid fa-circle-check"></i> Complete Sale & Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterPosItems() {
    const query = document.getElementById('posCatalogSearch').value.toLowerCase();
    const items = document.querySelectorAll('.pos-item-card');

    items.forEach(item => {
        const text = item.getAttribute('data-name');
        if (text.includes(query)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
