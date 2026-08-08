<?php
$path_prefix = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$query = $_GET['q'] ?? '';
$medicines = get_all_medicines();

if (!empty($query)) {
    $q = strtolower($query);
    $medicines = array_filter($medicines, function($m) use ($q) {
        return strpos(strtolower($m['medicine_name']), $q) !== false ||
               strpos(strtolower($m['generic_name']), $q) !== false ||
               strpos(strtolower($m['category_name']), $q) !== false ||
               strpos(strtolower($m['manufacturer']), $q) !== false ||
               strpos(strtolower($m['batch_number']), $q) !== false;
    });
}
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-magnifying-glass-plus" style="color:var(--primary);"></i> Dedicated Medicine Search</h1>
                <p>Lookup drugs by brand name, active generic formula, manufacturer, or category.</p>
            </div>
        </div>

        <!-- Search Input Bar -->
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-body">
                <form method="GET" action="medicine_search.php" style="display:flex; gap:0.75rem;">
                    <div class="search-box" style="flex:1;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="q" class="form-control" style="padding-left:2.5rem;" placeholder="Search Napa, Paracetamol, Square, Analgesics..." value="<?php echo htmlspecialchars($query); ?>" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-magnifying-glass"></i> Search Catalog
                    </button>
                    <?php if (!empty($query)): ?>
                        <a href="medicine_search.php" class="btn btn-outline btn-lg">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-list-ul"></i> Search Results <?php echo !empty($query) ? "for '" . htmlspecialchars($query) . "'" : ""; ?> (<?php echo count($medicines); ?> matches)
                </div>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Generic Composition</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Price</th>
                                <th>Available Qty</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Quick POS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($medicines)): ?>
                                <tr>
                                    <td colspan="9" style="text-align:center; padding:2rem; color:#94a3b8;">
                                        <i class="fa-solid fa-face-frown" style="font-size:2rem; margin-bottom:0.5rem;"></i>
                                        <p>No medicines match your search filter.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($medicines as $med): ?>
                                <tr>
                                    <td>
                                        <strong style="font-size:0.95rem; color:var(--primary-dark);"><?php echo htmlspecialchars($med['medicine_name']); ?></strong>
                                    </td>
                                    <td><em><?php echo htmlspecialchars($med['generic_name']); ?></em></td>
                                    <td><?php echo htmlspecialchars($med['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($med['manufacturer']); ?></td>
                                    <td><strong>$<?php echo number_format($med['selling_price'], 2); ?></strong></td>
                                    <td>
                                        <span style="font-weight:600;"><?php echo $med['stock_quantity']; ?> Units</span>
                                    </td>
                                    <td><?php echo $med['expiry_date']; ?></td>
                                    <td>
                                        <?php 
                                        if ($med['status'] == 'In Stock') echo '<span class="badge badge-in-stock">In Stock</span>';
                                        else if ($med['status'] == 'Low Stock') echo '<span class="badge badge-low-stock">Low Stock</span>';
                                        else if ($med['status'] == 'Out of Stock') echo '<span class="badge badge-out-stock">Out of Stock</span>';
                                        else echo '<span class="badge badge-expired">Expired</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="../sales/new_sale.php" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-cart-plus"></i> Add to POS
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
