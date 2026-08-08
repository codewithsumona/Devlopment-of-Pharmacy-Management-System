<?php
$role = $_SESSION['user_role'] ?? 'Admin';
?>
<header class="navbar">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="globalSearchInput" placeholder="Quick search medicine, batch, or invoice..." onkeyup="handleGlobalSearch(event)">
    </div>

    <div class="navbar-right">
        <!-- Quick POS Button -->
        <a href="<?php echo $path_prefix ?? ''; ?>sales/new_sale.php" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-cart-plus"></i> POS Terminal
        </a>

        <!-- Live Role Switcher Demo Button for Presentation -->
        <form method="POST" action="" style="display:inline;" id="roleSwitchForm">
            <input type="hidden" name="toggle_demo_role" value="1">
            <button type="submit" class="role-switcher-btn" title="Click to toggle user role view for Lab Presentation!">
                <i class="fa-solid fa-arrows-rotate"></i>
                Role: <strong><?php echo htmlspecialchars($role); ?> Mode</strong>
            </button>
        </form>

        <!-- Notification Dropdown -->
        <button class="nav-icon-btn" onclick="toggleNotifications()" title="Notifications">
            <i class="fa-regular fa-bell"></i>
            <span class="dot"></span>
        </button>

        <!-- User Profile Quick Menu -->
        <a href="<?php echo $path_prefix ?? ''; ?>profile/profile.php" class="nav-icon-btn" title="Profile Settings">
            <i class="fa-regular fa-circle-user"></i>
        </a>
    </div>
</header>

<?php
// Handle Role Toggle POST request seamlessly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_demo_role'])) {
    if ($_SESSION['user_role'] === 'Admin') {
        $_SESSION['user_role'] = 'Pharmacist';
        $_SESSION['user_name'] = 'Alex Rivera, PharmD';
        $_SESSION['user_email'] = 'alex@pharma.com';
    } else {
        $_SESSION['user_role'] = 'Admin';
        $_SESSION['user_name'] = 'Dr. Sarah Jenkins';
        $_SESSION['user_email'] = 'admin@pharma.com';
    }
    echo "<script>window.location.href = window.location.href;</script>";
    exit;
}
?>
