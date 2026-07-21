<?php
include "header.php";

// ==================== SECURITY & VALIDATION ====================
if (!isset($userdata) || empty($userdata['id'])) {
    $_SESSION['error_msg'] = "Session expired. Please login again.";
    header("Location: login.php");
    exit;
}

$user_id = (int)$userdata['id'];
$mobile = isset($userdata['mobile']) ? mysqli_real_escape_string($conn, $userdata['mobile']) : '';

// ==================== FETCH RECENT TRANSACTIONS ====================
$query_recent = $conn->prepare("SELECT create_date, order_id, customer_mobile, utr, amount, status FROM orders WHERE user_id = ? ORDER BY create_date DESC LIMIT 10");
$recent_transactions = [];
if ($query_recent) {
    $query_recent->bind_param("i", $user_id);
    $query_recent->execute();
    $result = $query_recent->get_result();
    while ($row = $result->fetch_assoc()) {
        $recent_transactions[] = $row;
    }
    $query_recent->close();
}

// ==================== COUNT TOTAL TRANSACTIONS ====================
$rowCount = 0;
if (!empty($mobile)) {
    $stmt_count = $conn->prepare("SELECT COUNT(*) AS count FROM reports WHERE mobile = ?");
    if ($stmt_count) {
        $stmt_count->bind_param("s", $mobile);
        $stmt_count->execute();
        $result = $stmt_count->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $rowCount = (int)$row['count'];
        }
        $stmt_count->close();
    }
}

// ==================== SUBSCRIPTION STATUS ====================
$expiryDate = $userdata['expiry'] ?? '';
$today = date('Y-m-d');
$planStatus = (!empty($expiryDate) && strtotime($expiryDate) >= strtotime($today)) ? "Active" : "Expired";

// ==================== FETCH ANNOUNCEMENTS ====================
$announcements = [];
$announcement_query = $conn->query("SELECT * FROM announcements ORDER BY id DESC LIMIT 3");
if ($announcement_query && $announcement_query->num_rows > 0) {
    while ($ann = $announcement_query->fetch_assoc()) {
        $announcements[] = $ann;
    }
}

// ==================== FETCH ORDER STATS ====================
$u_succ = $u_fail = $u_pend = 0;

$succ_stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE user_id = ? AND status = 'SUCCESS'");
if ($succ_stmt) {
    $succ_stmt->bind_param("i", $user_id);
    $succ_stmt->execute();
    $result = $succ_stmt->get_result();
    $u_succ = $result->fetch_assoc()['c'] ?? 0;
    $succ_stmt->close();
}

$fail_stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE user_id = ? AND status = 'FAILURE'");
if ($fail_stmt) {
    $fail_stmt->bind_param("i", $user_id);
    $fail_stmt->execute();
    $result = $fail_stmt->get_result();
    $u_fail = $result->fetch_assoc()['c'] ?? 0;
    $fail_stmt->close();
}

$pend_stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE user_id = ? AND status = 'PENDING'");
if ($pend_stmt) {
    $pend_stmt->bind_param("i", $user_id);
    $pend_stmt->execute();
    $result = $pend_stmt->get_result();
    $u_pend = $result->fetch_assoc()['c'] ?? 0;
    $pend_stmt->close();
}

// ==================== TODAY'S STATS ====================
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

$todaysuccesspayment = 0;
$todayallpayment = 0;
$todaypendingpayment = 0;
$todayfail = 0;

$ts_stmt = $conn->prepare("SELECT SUM(amount) as amt FROM orders WHERE user_id = ? AND status = 'SUCCESS' AND create_date BETWEEN ? AND ?");
if ($ts_stmt) {
    $ts_stmt->bind_param("iss", $user_id, $today_start, $today_end);
    $ts_stmt->execute();
    $result = $ts_stmt->get_result();
    $todaysuccesspayment = (float)($result->fetch_assoc()['amt'] ?? 0);
    $ts_stmt->close();
}

$ta_stmt = $conn->prepare("SELECT COUNT(*) as amt FROM orders WHERE user_id = ? AND status = 'SUCCESS' AND create_date BETWEEN ? AND ?");
if ($ta_stmt) {
    $ta_stmt->bind_param("iss", $user_id, $today_start, $today_end);
    $ta_stmt->execute();
    $result = $ta_stmt->get_result();
    $todayallpayment = (int)($result->fetch_assoc()['amt'] ?? 0);
    $ta_stmt->close();
}

$tp_stmt = $conn->prepare("SELECT SUM(amount) as amt FROM orders WHERE user_id = ? AND status = 'PENDING' AND create_date BETWEEN ? AND ?");
if ($tp_stmt) {
    $tp_stmt->bind_param("iss", $user_id, $today_start, $today_end);
    $tp_stmt->execute();
    $result = $tp_stmt->get_result();
    $todaypendingpayment = (float)($result->fetch_assoc()['amt'] ?? 0);
    $tp_stmt->close();
}

$tf_stmt = $conn->prepare("SELECT SUM(amount) as amt FROM orders WHERE user_id = ? AND status = 'FAILURE' AND create_date BETWEEN ? AND ?");
if ($tf_stmt) {
    $tf_stmt->bind_param("iss", $user_id, $today_start, $today_end);
    $tf_stmt->execute();
    $result = $tf_stmt->get_result();
    $todayfail = (float)($result->fetch_assoc()['amt'] ?? 0);
    $tf_stmt->close();
}

// ==================== ADMIN STATS ====================
$total_users_count = 0;
if (isset($userdata['role']) && strtolower($userdata['role']) === 'admin') {
    $usr_query = $conn->query("SELECT COUNT(*) as c FROM users");
    $total_users_count = $usr_query ? $usr_query->fetch_assoc()['c'] : 0;
}

// ==================== WEEKLY DATA FOR CHART ====================
$weekly_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $start = $date . ' 00:00:00';
    $end = $date . ' 23:59:59';
    
    $stmt = $conn->prepare("SELECT SUM(amount) as amt FROM orders WHERE user_id = ? AND status = 'SUCCESS' AND create_date BETWEEN ? AND ?");
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $start, $end);
        $stmt->execute();
        $result = $stmt->get_result();
        $weekly_data[] = (float)($result->fetch_assoc()['amt'] ?? 0);
        $stmt->close();
    }
}
$week_dates = [];
for ($i = 6; $i >= 0; $i--) {
    $week_dates[] = date('D', strtotime("-$i days"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --pi-primary: #093c31;
            --pi-primary-dark: #062b23;
            --pi-primary-light: #0d5540;
            --pi-accent: #d6eb5b;
            --pi-accent-dark: #c5d94f;
            --pi-text: #1e293b;
            --pi-text-light: #64748b;
            --pi-bg: #f8fafc;
            --pi-border: #e2e8f0;
            --pi-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --pi-hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--pi-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--pi-text);
        }

        /* Hero Banner */
        .pi-hero-card {
            background: linear-gradient(135deg, #093c31 0%, #0d5540 50%, #145c44 100%);
            border-radius: 24px;
            padding: 2rem 2rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(214, 235, 91, 0.2);
            box-shadow: 0 20px 40px rgba(9, 60, 49, 0.3);
        }

        .pi-hero-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(214, 235, 91, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .pi-hero-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .pi-hero-eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #d6eb5b;
            background: rgba(214, 235, 91, 0.12);
            border: 1px solid rgba(214, 235, 91, 0.25);
            border-radius: 30px;
            padding: 5px 14px;
            display: inline-block;
            margin-bottom: 16px;
        }

        .pi-hero-card h2 {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .pi-hero-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Stat Cards */
        .pi-stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--pi-border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pi-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--pi-hover-shadow);
            border-color: var(--pi-primary-light);
        }

        .pi-stat-card .stat-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pi-text-light);
            margin-bottom: 12px;
        }

        .pi-stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
        }

        .pi-stat-card .stat-trend {
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 20px;
            background: #f1f5f9;
        }

        /* Card Component */
        .pi-card {
            background: white;
            border-radius: 20px;
            border: 1px solid var(--pi-border);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .pi-card:hover {
            box-shadow: var(--pi-card-shadow);
        }

        /* Quick Actions List */
        .pi-list-compact {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pi-list-compact-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            color: var(--pi-text);
            text-decoration: none;
            border-bottom: 1px solid var(--pi-border);
            transition: all 0.2s ease;
        }

        .pi-list-compact-item:last-child {
            border-bottom: none;
        }

        .pi-list-compact-item:hover {
            color: var(--pi-primary);
            padding-left: 8px;
        }

        .pi-list-compact-item i:first-child {
            width: 24px;
            font-size: 1.1rem;
        }

        /* Badges */
        .pi-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .pi-badge-success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .pi-badge-warning {
            background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
            color: #854d0e;
        }

        .pi-badge-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .pi-badge-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        /* Alert */
        .announcement-alert {
            border-left: 4px solid var(--pi-accent);
            background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
            border-radius: 16px;
            padding: 1rem 1.25rem;
        }

        /* Table */
        .transaction-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .transaction-table th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pi-text-light);
            padding: 1rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid var(--pi-border);
        }

        .transaction-table td {
            padding: 1rem;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .transaction-table tr:hover td {
            background: #f8fafc;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pi-hero-card h2 {
                font-size: 1.5rem;
            }
            
            .pi-stat-card .stat-value {
                font-size: 1.3rem;
            }
            
            .pi-stat-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid px-4 py-3">
    
    <!-- Welcome Hero Banner -->
    <div class="pi-hero-card mb-4 animate-fade-in">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="pi-hero-eyebrow">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard Overview
                </span>
                <h2>
                    Welcome back, <?php echo htmlspecialchars(explode(' ', $userdata['name'])[0]); ?>!
                </h2>
                <p class="mb-0">
                    <i class="bi bi-calendar-check me-1"></i> 
                    <?php echo date('l, F j, Y'); ?> • 
                    <i class="bi bi-clock ms-2 me-1"></i>
                    Last login: <?php echo htmlspecialchars($userdata['last_login'] ?? 'Today'); ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-inline-flex gap-2">
                    <span class="pi-badge pi-badge-info">
                        <i class="bi bi-shield-check"></i> Verified
                    </span>
                    <span class="pi-badge pi-badge-success">
                        <i class="bi bi-star-fill"></i> <?php echo htmlspecialchars($userdata['plan'] ?? 'Premium'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <?php if (!empty($announcements)): ?>
    <div class="announcement-alert mb-4 animate-fade-in" style="animation-delay: 0.05s;">
        <div class="d-flex align-items-start gap-3">
            <div class="flex-shrink-0">
                <i class="bi bi-megaphone-fill text-warning fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Important Updates</h6>
                <div style="max-height: 60px; overflow-y: auto;">
                    <?php foreach($announcements as $ann): ?>
                    <div class="small mb-1">
                        <span class="fw-semibold">• <?php echo htmlspecialchars($ann['title']); ?>:</span>
                        <span class="text-muted"><?php echo htmlspecialchars($ann['message']); ?></span>
                        <span class="text-muted ms-2" style="font-size: 10px;"><?php echo date('d M', strtotime($ann['created_at'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex-shrink-0">
                <i class="bi bi-info-circle-fill text-primary"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Row 1 - Today's Performance -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="stat-label">
                    <i class="bi bi-wallet2 me-1"></i> Today's Revenue
                </div>
                <div class="stat-value text-success">
                    ₹<?php echo number_format($todaysuccesspayment, 2); ?>
                </div>
                <div class="mt-2">
                    <span class="stat-trend">
                        <i class="bi bi-arrow-up-short text-success"></i> 
                        +12.5%
                    </span>
                    <span class="text-muted ms-2 small">vs yesterday</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.15s;">
                <div class="stat-label">
                    <i class="bi bi-check-circle me-1"></i> Success Count
                </div>
                <div class="stat-value text-primary">
                    <?php echo number_format($todayallpayment); ?>
                </div>
                <div class="text-muted small mt-2">
                    <i class="bi bi-trophy"></i> Successful transactions today
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="stat-label">
                    <i class="bi bi-clock-history me-1"></i> Pending
                </div>
                <div class="stat-value text-warning">
                    ₹<?php echo number_format($todaypendingpayment, 2); ?>
                </div>
                <div class="text-muted small mt-2">
                    Awaiting confirmation
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.25s;">
                <div class="stat-label">
                    <i class="bi bi-x-circle me-1"></i> Failed
                </div>
                <div class="stat-value text-danger">
                    ₹<?php echo number_format($todayfail, 2); ?>
                </div>
                <div class="text-muted small mt-2">
                    Need attention
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row 2 - Account Status -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="stat-label">
                    <i class="bi bi-calendar-event me-1"></i> Plan Expiry
                </div>
                <div class="stat-value" style="font-size: 1.3rem;">
                    <?php echo !empty($expiryDate) ? date('d M Y', strtotime($expiryDate)) : 'N/A'; ?>
                </div>
                <div class="mt-2">
                    <span class="pi-badge <?php echo $planStatus == 'Active' ? 'pi-badge-success' : 'pi-badge-danger'; ?>">
                        <i class="bi <?php echo $planStatus == 'Active' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?>"></i>
                        <?php echo $planStatus; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.35s;">
                <div class="stat-label">
                    <i class="bi bi-graph-up me-1"></i> API Usage
                </div>
                <div class="stat-value" style="font-size: 1.3rem;">
                    <?php echo number_format($rowCount); ?>
                </div>
                <div class="text-muted small mt-2">
                    Total API calls made
                </div>
            </div>
        </div>
        <?php if (isset($userdata['role']) && strtolower($userdata['role']) === 'admin'): ?>
        <div class="col-6 col-lg-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="stat-label">
                    <i class="bi bi-people me-1"></i> Total Users
                </div>
                <div class="stat-value text-success" style="font-size: 1.3rem;">
                    <?php echo number_format($total_users_count); ?>
                </div>
                <div class="text-muted small mt-2">
                    Active merchants
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.45s;">
                <div class="stat-label">
                    <i class="bi bi-activity me-1"></i> System Health
                </div>
                <div class="stat-value text-primary" style="font-size: 1.3rem;">
                    99.9%
                </div>
                <div class="text-muted small mt-2">
                    Uptime this month
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="col-6 col-lg-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="stat-label">
                    <i class="bi bi-shield-check me-1"></i> Account Status
                </div>
                <div class="stat-value text-success" style="font-size: 1.3rem;">
                    Active
                </div>
                <div class="text-muted small mt-2">
                    Verified merchant
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pi-stat-card animate-fade-in" style="animation-delay: 0.45s;">
                <div class="stat-label">
                    <i class="bi bi-upc-scan me-1"></i> UPI Status
                </div>
                <div class="stat-value <?php echo ($userdata['hdfc_connected'] == 'Yes' || $userdata['phonepe_connected'] == 'Yes') ? 'text-success' : 'text-warning'; ?>" style="font-size: 1.3rem;">
                    <?php echo ($userdata['hdfc_connected'] == 'Yes' || $userdata['phonepe_connected'] == 'Yes') ? 'Active' : 'Setup Pending'; ?>
                </div>
                <div class="text-muted small mt-2">
                    Payment gateways
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Analytics & Quick Actions Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="pi-card p-4 h-100 animate-fade-in" style="animation-delay: 0.5s;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Weekly Performance
                    </h6>
                    <span class="text-muted small">
                        <i class="bi bi-calendar-week"></i> Last 7 days
                    </span>
                </div>
                <div style="position: relative; height: 220px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="pi-card p-4 h-100 animate-fade-in" style="animation-delay: 0.55s;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Transaction Overview
                    </h6>
                    <span class="text-muted small">
                        All time
                    </span>
                </div>
                <div style="position: relative; height: 220px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="pi-card p-4 h-100 animate-fade-in" style="animation-delay: 0.6s;">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-lightning-charge me-2 text-primary"></i>Quick Actions
                </h6>
                <div class="pi-list-compact">
                    <a href="connect_merchant" class="pi-list-compact-item">
                        <span><i class="bi bi-credit-card-2-front me-2 text-primary"></i>UPI Settings</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                    <a href="payment_link" class="pi-list-compact-item">
                        <span><i class="bi bi-link-45deg me-2 text-primary"></i>Payment Links</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                    <a href="transactions" class="pi-list-compact-item">
                        <span><i class="bi bi-bar-chart-line me-2 text-primary"></i>Reports & Exports</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                    <a href="apidetails" class="pi-list-compact-item">
                        <span><i class="bi bi-code-slash me-2 text-primary"></i>API Keys & Docs</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                    <a href="subscription" class="pi-list-compact-item">
                        <span><i class="bi bi-box-seam me-2 text-primary"></i>Subscription Plans</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                    <a href="profile" class="pi-list-compact-item">
                        <span><i class="bi bi-person me-2 text-primary"></i>Profile Settings</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="pi-card animate-fade-in" style="animation-delay: 0.65s;">
        <div class="d-flex flex-wrap flex-sm-row justify-content-between align-items-center p-4 pb-0 gap-2">
            <div>
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-receipt me-2 text-primary"></i>Recent Transactions
                </h6>
                <p class="text-muted mb-0 mt-1 small">Latest 10 orders processed through your account</p>
            </div>
            <a href="transactions" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Order ID</th>
                        <th>Customer Mobile</th>
                        <th>UTR Number</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_transactions)): ?>
                        <?php foreach($recent_transactions as $txn): ?>
                        <tr>
                            <td class="text-muted" style="white-space: nowrap;">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo date('d M, h:i A', strtotime($txn['create_date'])); ?>
                            </td>
                            <td>
                                <code class="fw-semibold" style="color: var(--pi-primary); background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 11px;">
                                    <?php echo htmlspecialchars($txn['order_id']); ?>
                                </code>
                            </td>
                            <td class="text-muted">
                                <?php echo htmlspecialchars($txn['customer_mobile'] ?: '—'); ?>
                            </td>
                            <td>
                                <?php if ($txn['utr']): ?>
                                    <code style="font-size: 11px; background: #f8fafc; padding: 4px 8px; border-radius: 6px;">
                                        <?php echo htmlspecialchars($txn['utr']); ?>
                                    </code>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold">
                                ₹<?php echo number_format($txn['amount'], 2); ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'pi-badge-warning';
                                $statusIcon = 'bi-clock-history';
                                if ($txn['status'] == 'SUCCESS') {
                                    $statusClass = 'pi-badge-success';
                                    $statusIcon = 'bi-check-circle';
                                } elseif ($txn['status'] == 'FAILURE') {
                                    $statusClass = 'pi-badge-danger';
                                    $statusIcon = 'bi-x-circle';
                                }
                                ?>
                                <span class="pi-badge <?php echo $statusClass; ?>">
                                    <i class="bi <?php echo $statusIcon; ?>"></i>
                                    <?php echo ucfirst(strtolower($txn['status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No transactions found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Performance Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($week_dates); ?>,
            datasets: [{
                label: 'Revenue (₹)',
                data: <?php echo json_encode($weekly_data); ?>,
                borderColor: '#093c31',
                backgroundColor: 'rgba(9, 60, 49, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#d6eb5b',
                pointBorderColor: '#093c31',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#d6eb5b'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return '₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e2e8f0',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Successful', 'Failed', 'Pending'],
            datasets: [{
                data: [<?php echo $u_succ; ?>, <?php echo $u_fail; ?>, <?php echo $u_pend; ?>],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 11,
                            weight: '600'
                        },
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = <?php echo $u_succ + $u_fail + $u_pend; ?>;
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include "footer.php"; ?>
</body>
</html>