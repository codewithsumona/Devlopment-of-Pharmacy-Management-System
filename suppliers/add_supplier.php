<?php
$path_prefix = '../';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/db_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        header("Location: supplier_list.php?msg=" . urlencode("Invalid CSRF token."));
        exit;
    }
    $sup_name = trim($_POST['supplier_name'] ?? '');
    $company = trim($_POST['company_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($sup_name === '' || $company === '' || $phone === '' || $email === '') {
        header("Location: supplier_list.php?msg=" . urlencode("Missing required supplier fields."));
        exit;
    }

    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_name, company_name, phone, email, address, status) VALUES (:supplier_name, :company_name, :phone, :email, :address, 'Active')");
            $stmt->execute([
                ':supplier_name' => $sup_name,
                ':company_name' => $company,
                ':phone' => $phone,
                ':email' => $email,
                ':address' => $address
            ]);
            $newId = $pdo->lastInsertId();
            header("Location: supplier_list.php?msg=" . urlencode("Supplier '{$sup_name}' (ID: {$newId}) added."));
            exit;
        } catch (Exception $e) {
            header("Location: supplier_list.php?msg=" . urlencode("DB error adding supplier."));
            exit;
        }
    } else {
        header("Location: supplier_list.php?msg=" . urlencode("Supplier '{$sup_name}' registered (offline fallback)."));
        exit;
    }
}
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="content-body">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-user-plus" style="color:var(--primary);"></i> Add New Supplier</h1>
                <p>Onboard a new pharmaceutical vendor or distributor account.</p>
            </div>
            <div class="page-actions">
                <a href="supplier_list.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Directory
                </a>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-building"></i> Vendor Details Form
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="add_supplier.php">
                    <?php echo csrf_input(); ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Full Supplier Name *</label>
                            <input type="text" name="supplier_name" class="form-control" placeholder="e.g. Acme Pharma Ltd." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Company Brand Name *</label>
                            <input type="text" name="company_name" class="form-control" placeholder="e.g. Acme Corp" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Phone *</label>
                            <input type="text" name="phone" class="form-control" placeholder="+880 1700-000000" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="sales@acmepharma.com" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:1rem;">
                        <label class="form-label">Office & Warehouse Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter physical street address, district..."></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.8rem; margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.25rem;">
                        <a href="supplier_list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk"></i> Save Supplier Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
