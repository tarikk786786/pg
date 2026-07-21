<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['username'])) {
    $mobile = $_SESSION['username'];
    $user = "SELECT * FROM users WHERE mobile = '$mobile'";
    $uu = mysqli_query($conn, $user);
    $userdata = mysqli_fetch_array($uu);
    
    $tdate = date("Y-m-d");
    $todayallpayment = $conn->query("SELECT COUNT(`id`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'SUCCESS' AND DATE(`create_date`) = '$tdate' ")->fetch_assoc();
    $todaysuccesspayment = $conn->query("SELECT SUM(`amount`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'SUCCESS' AND DATE(`create_date`) = '$tdate' ")->fetch_assoc();
    $todaypendingpayment = $conn->query("SELECT SUM(`amount`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'PENDING' AND DATE(`create_date`) = '$tdate' ")->fetch_assoc();
    
    $todayfail = $conn->query("SELECT SUM(`amount`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'FAILURE' AND DATE(`create_date`) = '$tdate' ")->fetch_assoc();
    
    $fixednavbar = $userdata["fixed_navbar"] ?? 0;
    $fixedlayout = $userdata["fixed_layout"] ?? 0;
    $fixedsidebar = $userdata["sidebar_layout"] ?? 0;
    $boxstyle = $userdata["box_style"] ?? 0;
    $themecolor = $userdata["theme_color"] ?? 'default';
    
    $server = $_SERVER["SERVER_NAME"];
    
    // Dynamic protocol & host
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = '';
    if (stripos($script_name, '/PAY/') === 0 || $script_name === '/PAY') {
        $base_dir = '/PAY';
    }
    $site_url = $protocol . $host . $base_dir;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_settings['brand_name'] ?? 'Dezo'); ?> | Merchant Panel</title>
    <!-- Premium Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="Dezo.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>

<body class="pi-shell merchant-shell">

    <!-- Loading Backdrops -->
    <div class="rl-loading-container d-none" id="loading_ajax">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation -->
    <aside class="pi-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($site_settings['brand_name'] ?? 'Dezo'); ?></h5>
            </div>
            <small>Merchant Panel</small>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-label">Main</li>
            <li class="menu-item">
                <a href="dashboard" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="menu-item">
                <a href="connect_merchant" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'connect_merchant.php') ? 'active' : ''; ?>">
                    <i class="bi bi-credit-card-2-front"></i> Connect Merchant
                </a>
            </li>

            <?php if ($userdata['role'] == 'Admin') { ?>
            <li class="menu-label">Admin Management</li>
            <li class="menu-item">
                <a href="add_merchant" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'add_merchant.php') ? 'active' : ''; ?>">
                    <i class="bi bi-person-add"></i> Add User
                </a>
            </li>
            <li class="menu-item">
                <a href="merchant_list" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'merchant_list.php') ? 'active' : ''; ?>">
                    <i class="bi bi-list-check"></i> API User List
                </a>
            </li>
            <li class="menu-item">
                <a href="sitesetting" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'sitesetting.php') ? 'active' : ''; ?>">
                    <i class="bi bi-globe"></i> Website Management
                </a>
            </li>
            <li class="menu-item">
                <a href="manage_subscription" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_subscription.php') ? 'active' : ''; ?>">
                    <i class="bi bi-shop"></i> Subscription Management
                </a>
            </li>
            <li class="menu-item">
                <a href="add_api" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'add_api.php') ? 'active' : ''; ?>">
                    <i class="bi bi-whatsapp"></i> SMTP / WhatsApp Management
                </a>
            </li>
            
            <li class="menu-label">Admin Advanced</li>
            <li class="menu-item">
                <a href="reports" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                    <i class="bi bi-graph-up"></i> Analyst (Platform Analytics)
                </a>
            </li>
            <li class="menu-item">
                <a href="admin_payouts" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_payouts.php') ? 'active' : ''; ?>">
                    <i class="bi bi-bank"></i> Payout Requests
                </a>
            </li>
            <li class="menu-item">
                <a href="support_admin" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'support_admin.php') ? 'active' : ''; ?>">
                    <i class="bi bi-chat-square-text"></i> Support Tickets
                </a>
            </li>
            <li class="menu-item">
                <a href="manage_announcements" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_announcements.php') ? 'active' : ''; ?>">
                    <i class="bi bi-megaphone"></i> Broadcasts
                </a>
            </li>
            <li class="menu-item">
                <a href="audit_logs" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'audit_logs.php') ? 'active' : ''; ?>">
                    <i class="bi bi-shield-lock"></i> Audit Logs
                </a>
            </li>
            <?php } ?>

            <li class="menu-label">Features</li>
            <li class="menu-item">
                <a href="payment_link" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'payment_link.php') ? 'active' : ''; ?>">
                    <i class="bi bi-link-45deg"></i> Payment Links
                </a>
            </li>
            <li class="menu-item">
                <a href="profile" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
            </li>
            <li class="menu-item">
                <a href="transactions" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'active' : ''; ?>">
                    <i class="bi bi-bar-chart-line"></i> User Reports
                </a>
            </li>
            <li class="menu-item">
                <a href="user_wallet" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'user_wallet.php') ? 'active' : ''; ?>">
                    <i class="bi bi-wallet2"></i> My Wallet
                </a>
            </li>
            <li class="menu-item">
                <a href="support" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'support.php') ? 'active' : ''; ?>">
                    <i class="bi bi-headset"></i> Helpdesk
                </a>
            </li>
            
            <li class="menu-label">Integration & Account</li>
            <li class="menu-item">
                <a href="theme_settings" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'theme_settings.php') ? 'active' : ''; ?>">
                    <i class="bi bi-palette"></i> Theme Settings
                </a>
            </li>
            <li class="menu-item">
                <a href="apidetails" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'apidetails.php') ? 'active' : ''; ?>">
                    <i class="bi bi-code-slash"></i> Developers / API
                </a>
            </li>
            <li class="menu-item">
                <a href="simple_code" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'simple_code.php') ? 'active' : ''; ?>">
                    <i class="bi bi-cart-check"></i> Simple Code
                </a>
            </li>
            <li class="menu-item">
                <a href="subscription" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'subscription.php') ? 'active' : ''; ?>">
                    <i class="bi bi-box-seam"></i> Subscription Plans
                </a>
            </li>
            <li class="menu-item">
                <a href="change_password" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'change_password.php') ? 'active' : ''; ?>">
                    <i class="bi bi-shield-lock"></i> Change Password
                </a>
            </li>
            <li class="menu-item" style="margin-top:16px;">
                <a href="logout" class="text-danger">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="pi-main">
        <!-- Topbar -->
        <div class="pi-topbar">
            <div class="pi-topbar-main d-flex align-items-center">
                <button class="pi-hamburger d-lg-none" id="sidebarToggle" aria-label="Toggle menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
                <h5 class="page-title mb-0 ms-2">Dashboard</h5>
            </div>
            <div class="pi-topbar-actions d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <a href="<?php echo $site_url; ?>" class="pi-topbar-link d-none d-lg-inline-flex">
                    <i class="bi bi-globe2"></i>
                    <span>View Site</span>
                </a>
                
                <span class="pi-status-pill pi-status-pill-success">
                    <i class="bi bi-check-circle me-1"></i>Active
                </span>
                
                <div class="dropdown pi-topbar-dropdown">
                    <button type="button" class="pi-topbar-user btn p-0 border-0 shadow-none text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="pi-user-avatar">
                            <?php echo strtoupper(substr($userdata['name'], 0, 1)); ?>
                        </div>
                        <div class="pi-user-info d-none d-sm-block text-start" style="max-width:148px;">
                            <div class="pi-user-name text-truncate"><?php echo htmlspecialchars($userdata['name']); ?></div>
                            <div class="pi-user-role text-truncate"><?php echo htmlspecialchars($userdata['role']); ?></div>
                        </div>
                        <i class="bi bi-chevron-down pi-user-chevron d-none d-sm-block ms-1" style="font-size: 10px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pi-user-dropdown mt-2">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-semibold" style="font-size:13px;"><?php echo htmlspecialchars($userdata['name']); ?></div>
                            <div class="text-muted" style="font-size:11.5px;"><?php echo htmlspecialchars($userdata['email']); ?></div>
                        </li>
                        <li><a class="dropdown-item py-2" href="profile"><i class="bi bi-person me-2 text-primary"></i>Profile</a></li>
                        <li><a class="dropdown-item py-2" href="subscription"><i class="bi bi-box-seam me-2 text-primary"></i>Subscription</a></li>
                        <li><a class="dropdown-item py-2" href="apidetails"><i class="bi bi-code-slash me-2 text-primary"></i>API Keys</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item text-danger py-2" href="logout">
                                <i class="bi bi-box-arrow-left me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="pi-content">
<?php
} else {
    header("location:index");
    exit;
}
?>
