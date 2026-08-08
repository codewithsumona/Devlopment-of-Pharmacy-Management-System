<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$role = $_SESSION['user_role'] ?? 'Admin';
?>
<aside class="sidebar" id="appSidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-icon">
            <i class="fa-solid fa-prescription-bottle-medical"></i>
        </div>
        <div class="sidebar-brand">
            <h2>PharmaCare</h2>
            <span>PRO Prototype v1.0</span>
        </div>
    </div>

    <div class="sidebar-menu">
        <div class="menu-category">Main Menu</div>
        
        <a href="<?php echo $path_prefix ?? ''; ?>dashboard.php" class="sidebar-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <?php if ($role === 'Pharmacist' || $role === 'Staff'): ?>
            <!-- Pharmacist / Staff Specific Menu -->
            <a href="<?php echo $path_prefix ?? ''; ?>search/medicine_search.php" class="sidebar-nav-item <?php echo ($current_page == 'medicine_search.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-magnifying-glass-plus"></i>
                <span>Search Medicine</span>
            </a>
            
            <a href="<?php echo $path_prefix ?? ''; ?>inventory/inventory.php" class="sidebar-nav-item <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Check Stock</span>
            </a>

            <div class="menu-category">Sales & Transactions</div>

            <a href="<?php echo $path_prefix ?? ''; ?>sales/new_sale.php" class="sidebar-nav-item <?php echo ($current_page == 'new_sale.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-cash-register"></i>
                <span>Create Sale (POS)</span>
                <span class="badge-counter badge-warning">POS</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>sales/sales_history.php" class="sidebar-nav-item <?php echo ($current_page == 'sales_history.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-receipt"></i>
                <span>View Sales</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>purchases/purchase_list.php" class="sidebar-nav-item <?php echo ($current_page == 'purchase_list.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck-ramp-box"></i>
                <span>Purchase Info</span>
            </a>

        <?php else: ?>
            <!-- Admin Full Access Menu -->
            <div class="menu-category">Pharmacy Control</div>

            <a href="<?php echo $path_prefix ?? ''; ?>medicines/medicine_list.php" class="sidebar-nav-item <?php echo ($current_dir == 'medicines' && $current_page == 'medicine_list.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-pills"></i>
                <span>Medicine List</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>medicines/add_medicine.php" class="sidebar-nav-item <?php echo ($current_page == 'add_medicine.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Add Medicine</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>inventory/inventory.php" class="sidebar-nav-item <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventory / Stock</span>
                <span class="badge-counter badge-danger">2 Low</span>
            </a>

            <div class="menu-category">Sales & Purchases</div>

            <a href="<?php echo $path_prefix ?? ''; ?>sales/new_sale.php" class="sidebar-nav-item <?php echo ($current_page == 'new_sale.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-cash-register"></i>
                <span>New Sale (POS)</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>sales/sales_history.php" class="sidebar-nav-item <?php echo ($current_page == 'sales_history.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Sales History</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>purchases/purchase_list.php" class="sidebar-nav-item <?php echo ($current_page == 'purchase_list.php' || $current_page == 'add_purchase.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck-field"></i>
                <span>Purchases</span>
            </a>

            <div class="menu-category">Administration</div>

            <a href="<?php echo $path_prefix ?? ''; ?>suppliers/supplier_list.php" class="sidebar-nav-item <?php echo ($current_dir == 'suppliers') ? 'active' : ''; ?>">
                <i class="fa-solid fa-building-user"></i>
                <span>Suppliers</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>staff/staff_list.php" class="sidebar-nav-item <?php echo ($current_dir == 'staff') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users-gear"></i>
                <span>Staff Management</span>
            </a>

            <a href="<?php echo $path_prefix ?? ''; ?>reports/reports.php" class="sidebar-nav-item <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Analytics & Reports</span>
            </a>
        <?php endif; ?>

        <div class="menu-category">Account</div>

        <a href="<?php echo $path_prefix ?? ''; ?>profile/profile.php" class="sidebar-nav-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-gear"></i>
            <span>My Profile</span>
        </a>

        <a href="<?php echo $path_prefix ?? ''; ?>login.php?logout=1" class="sidebar-nav-item">
            <i class="fa-solid fa-right-from-bracket" style="color: #f87171;"></i>
            <span style="color: #f87171;">Logout</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile-summary">
            <div class="avatar">
                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'Admin', 0, 1)); ?>
            </div>
            <div class="user-info">
                <div class="name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Dr. Sarah Jenkins'); ?></div>
                <div class="role"><i class="fa-solid fa-shield-halved"></i> <?php echo htmlspecialchars($role); ?></div>
            </div>
        </div>
    </div>
</aside>
