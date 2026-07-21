<?php 
include "header.php"; 

// ==================== SECURITY & VALIDATION ====================
// Ensure user is logged in
if (!isset($userdata) || empty($userdata['id'])) {
    $_SESSION['error_msg'] = "Session expired. Please login again.";
    header("Location: login.php");
    exit;
}

$user_id = (int)$userdata['id']; // Cast to integer for SQL injection prevention

// ==================== FETCH ALL SUBSCRIPTION PLANS ====================
$all_plans = [];
$plan_map = []; // amount => plan data
$sub_amounts = []; // Store amounts for matching
$plan_ids = []; // Store plan IDs for queries

// Use prepared statement for security
$plan_query = "SELECT * FROM subscription_plan ORDER BY CAST(amount AS UNSIGNED) ASC";
$result = $conn->query($plan_query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_plans[] = $row;
        $amount_int = (int)$row['amount'];
        $plan_map[$amount_int] = $row;
        $sub_amounts[] = $amount_int;
        $plan_ids[] = (int)$row['id'];
    }
}

// If no plans found, show error
if (empty($all_plans)) {
    echo '<div class="alert alert-danger">No subscription plans found. Please contact support.</div>';
    include "footer.php";
    exit;
}

// ==================== DETECT CURRENT USER PLAN ====================
$user_sub_plan_name = "Enterprise"; // Default
$user_sub_plan_id = null;

// Method 1: Check users table first (most reliable)
if (!empty($userdata['plan'])) {
    $user_sub_plan_name = $userdata['plan'];
    // Find the plan ID
    foreach ($all_plans as $plan) {
        if ($plan['plan_name'] === $user_sub_plan_name) {
            $user_sub_plan_id = $plan['id'];
            break;
        }
    }
}

// Method 2: If not found in users table, get from latest successful order
if (empty($user_sub_plan_id) && !empty($plan_ids)) {
    $placeholders = implode(',', array_fill(0, count($plan_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status = 'SUCCESS' AND plan_id IN ($placeholders) ORDER BY create_date DESC LIMIT 1");
    
    if ($stmt) {
        $types = 'i' . str_repeat('i', count($plan_ids));
        $params = array_merge([$user_id], $plan_ids);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $latest_order = $result->fetch_assoc();
        
        if ($latest_order && !empty($latest_order['plan_id'])) {
            foreach ($all_plans as $plan) {
                if ($plan['id'] == $latest_order['plan_id']) {
                    $user_sub_plan_name = $plan['plan_name'];
                    $user_sub_plan_id = $plan['id'];
                    break;
                }
            }
        }
        $stmt->close();
    }
}

// Method 3: Match by amount if plan_id not found
if (empty($user_sub_plan_id) && !empty($sub_amounts)) {
    $amounts_str = implode(',', $sub_amounts);
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status = 'SUCCESS' AND amount IN ($amounts_str) ORDER BY create_date DESC LIMIT 1");
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $latest_order = $result->fetch_assoc();
        
        if ($latest_order) {
            $amount_int = (int)$latest_order['amount'];
            if (isset($plan_map[$amount_int])) {
                $user_sub_plan_name = $plan_map[$amount_int]['plan_name'];
                $user_sub_plan_id = $plan_map[$amount_int]['id'];
            }
        }
        $stmt->close();
    }
}

// ==================== CALCULATE SUBSCRIPTION STATUS ====================
$expiry_date = $userdata['expiry'] ?? null;
$is_active = false;
$days_left = 0;
$expiry_fmt = 'N/A';

if ($expiry_date && $expiry_date !== '0000-00-00' && $expiry_date !== '1970-01-01') {
    $today = new DateTime();
    $expiry_obj = new DateTime($expiry_date);
    
    if ($expiry_obj >= $today) {
        $diff = $today->diff($expiry_obj);
        $days_left = $diff->days;
        $is_active = true;
    } else {
        $is_active = false;
        $days_left = 0;
    }
    $expiry_fmt = date('d M Y', strtotime($expiry_date));
}

// ==================== FETCH PURCHASE HISTORY ====================
$history_data = [];

if (!empty($plan_ids) && !empty($sub_amounts)) {
    $amounts_str = implode(',', array_unique($sub_amounts));
    $plan_placeholders = implode(',', array_fill(0, count($plan_ids), '?'));
    
    // Prepare query to get orders matching either plan_id or amount
    $history_query = "SELECT * FROM orders 
                      WHERE user_id = ? 
                      AND (
                          plan_id IN ($plan_placeholders) 
                          OR amount IN ($amounts_str)
                      )
                      ORDER BY create_date DESC 
                      LIMIT 30";
    
    $stmt = $conn->prepare($history_query);
    
    if ($stmt) {
        $types = 'i' . str_repeat('i', count($plan_ids));
        $params = array_merge([$user_id], $plan_ids);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $history_data[] = $row;
        }
        $stmt->close();
    }
}

// ==================== DETERMINE FEATURED PLAN ====================
$plan_count = count($all_plans);
$featured_idx = $plan_count > 0 ? (int)floor($plan_count / 2) : -1;

// ==================== DEFINE PLAN FEATURES ====================
$plan_features = [
    ['zero_txn_fee', 'bi-percent', '0 Transaction Fee'],
    ['realtime_txn', 'bi-lightning-fill', 'Realtime Transaction'],
    ['no_amount_limit', 'bi-infinity', 'No Amount Limit'],
    ['hdfc_vyapar', 'bi-bank', 'HDFC Vyapar'],
    ['dynamic_qr', 'bi-qr-code', 'Dynamic QR Code'],
    ['direct_upi', 'bi-phone', 'Direct UPI Intent'],
    ['accept_all_upi', 'bi-wallet2', 'Accept All UPI Apps'],
    ['support_247', 'bi-headset', '24x7 Support'],
    ['instant_settlement', 'bi-arrow-down-circle', 'Instant Settlement'],
    ['api_access', 'bi-code-slash', 'API Access'],
    ['custom_checkout', 'bi-brush', 'Customised Checkout'],
    ['whatsapp_support', 'bi-whatsapp', 'WhatsApp Support'],
    ['branding', 'bi-star-fill', 'Branding & Identity']
];
?>

<style>
/* ======== CSS Variables ======== */
:root {
    --pi-primary: #093c31;
    --pi-primary-light: #0d5540;
    --pi-accent: #d6eb5b;
    --pi-text: #1e293b;
    --pi-text-muted: #64748b;
    --pi-border: #e2e8f0;
    --pi-bg: #f8fafc;
}

/* ======== Subscription Hero ======== */
.sub-hero {
    background: linear-gradient(135deg, #093c31 0%, #0d5540 40%, #145c44 70%, #0a4535 100%);
    border-radius: 20px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    border: 1px solid rgba(214, 235, 91, 0.1);
    box-shadow: 0 20px 60px rgba(9, 60, 49, 0.4);
}

.sub-hero::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(214, 235, 91, 0.08) 0%, transparent 70%);
    border-radius: 50%;
}

.sub-hero::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: -50px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.04) 0%, transparent 70%);
    border-radius: 50%;
}

.sub-hero-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #d6eb5b;
    background: rgba(214, 235, 91, 0.12);
    border: 1px solid rgba(214, 235, 91, 0.25);
    border-radius: 30px;
    padding: 4px 14px;
    display: inline-block;
    margin-bottom: 12px;
}

.sub-hero h2 {
    font-size: 2.4rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
    line-height: 1.1;
}

.sub-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.95rem;
    margin-bottom: 0;
}

.sub-hero-badge {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 18px;
}

.sub-hero-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 50px;
    padding: 6px 16px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 12.5px;
    font-weight: 500;
}

.sub-hero-pill i {
    font-size: 13px;
    color: #d6eb5b;
}

/* Status Cards */
.sub-stat-card {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.4rem 1.6rem;
    backdrop-filter: blur(10px);
    height: 100%;
}

.sub-stat-label {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.45);
    margin-bottom: 8px;
}

.sub-stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}

.sub-stat-sub {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.45);
}

.sub-stat-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
}

.sub-stat-status .status-dot {
    width: 10px;
    height: 10px;
    background: #d6eb5b;
    border-radius: 50%;
    box-shadow: 0 0 8px rgba(214, 235, 91, 0.7);
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {

    0%,
    100% {
        transform: scale(1);
        opacity: 1;
    }

    50% {
        transform: scale(1.3);
        opacity: 0.7;
    }
}

/* Plan Cards */
.plans-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--pi-text);
    margin-bottom: 1.2rem;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--pi-border);
    display: flex;
    align-items: center;
    gap: 8px;
}

.plan-card {
    background: #fff;
    border: 2px solid var(--pi-border);
    border-radius: 20px;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.plan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 50px rgba(9, 60, 49, 0.12);
    border-color: #093c31;
}

.plan-card.featured {
    background: linear-gradient(160deg, #093c31 0%, #0d5540 100%);
    border-color: #d6eb5b;
    box-shadow: 0 20px 60px rgba(9, 60, 49, 0.35);
    transform: translateY(-6px);
}

.plan-card.featured:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 70px rgba(9, 60, 49, 0.45);
}

.featured-badge {
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    background: #d6eb5b;
    color: #093c31;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 4px 18px;
    border-radius: 0 0 12px 12px;
}

.plan-name {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--pi-text-muted);
    margin-bottom: 8px;
}

.plan-card.featured .plan-name {
    color: rgba(255, 255, 255, 0.55);
}

.plan-price {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--pi-text);
    line-height: 1;
    margin-bottom: 2px;
}

.plan-card.featured .plan-price {
    color: #fff;
}

.plan-duration {
    font-size: 13px;
    color: var(--pi-text-muted);
    margin-bottom: 1.5rem;
}

.plan-card.featured .plan-duration {
    color: rgba(255, 255, 255, 0.5);
}

.plan-divider {
    border: none;
    border-top: 1px solid var(--pi-border);
    margin: 0 0 1.2rem;
}

.plan-card.featured .plan-divider {
    border-color: rgba(255, 255, 255, 0.1);
}

.plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem;
    flex-grow: 1;
}

.plan-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    color: #4b5563;
    padding: 5px 0;
}

.plan-card.featured .plan-features li {
    color: rgba(255, 255, 255, 0.8);
}

.plan-features li .feat-icon {
    font-size: 14px;
    flex-shrink: 0;
}

.feat-ok {
    color: #16a34a;
}

.feat-no {
    color: #dc2626;
}

.plan-card.featured .feat-ok {
    color: #d6eb5b;
}

.plan-card.featured .feat-no {
    color: rgba(255, 255, 255, 0.3);
}

.plan-features li.feat-disabled {
    color: #9ca3af;
    text-decoration: line-through;
}

.plan-card.featured .plan-features li.feat-disabled {
    color: rgba(255, 255, 255, 0.3);
}

.plan-btn {
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 14px;
    border: 2px solid #093c31;
    background: transparent;
    color: #093c31;
    transition: all 0.25s ease;
    cursor: pointer;
}

.plan-btn:hover {
    background: #093c31;
    color: #fff;
}

.plan-card.featured .plan-btn {
    background: #d6eb5b;
    color: #093c31;
    border-color: #d6eb5b;
}

.plan-card.featured .plan-btn:hover {
    background: #c5d94f;
    border-color: #c5d94f;
}

/* Purchase History */
.history-card {
    background: #fff;
    border: 1px solid var(--pi-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.history-card-header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid var(--pi-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fafbfc;
}

.history-card-header h6 {
    font-weight: 700;
    font-size: 15px;
    color: var(--pi-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
}

.history-table th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--pi-text-muted);
    padding: 12px 16px;
    border-bottom: 1px solid var(--pi-border);
    white-space: nowrap;
}

.history-table td {
    padding: 13px 16px;
    font-size: 13.5px;
    color: var(--pi-text);
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.history-table tr:last-child td {
    border-bottom: none;
}

.history-table tr:hover td {
    background: #f8fafc;
}

.order-id-link {
    color: #d6eb5b;
    background: #093c31;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
    font-family: monospace;
    text-decoration: none;
    display: inline-block;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11.5px;
    font-weight: 700;
}

.status-success {
    background: #dcfce7;
    color: #16a34a;
}

.status-pending {
    background: #fef9c3;
    color: #ca8a04;
}

.status-failed {
    background: #fee2e2;
    color: #dc2626;
}

.status-gateway {
    color: #64748b;
    font-size: 13px;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--pi-text-muted);
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    opacity: 0.4;
}

/* Responsive */
@media (max-width: 768px) {
    .sub-hero {
        padding: 1.5rem;
    }

    .sub-hero h2 {
        font-size: 1.8rem;
    }

    .plan-card {
        padding: 1.5rem;
    }

    .plan-price {
        font-size: 2rem;
    }

    .history-table th,
    .history-table td {
        padding: 8px 12px;
        font-size: 12px;
    }
}
</style>

<!-- ==================== HERO BANNER ==================== -->
<div class="container-fluid px-4">
    <div class="sub-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <span class="sub-hero-eyebrow"><i class="bi bi-box-seam me-1"></i>Membership Plans</span>
                <h2><?php echo strtoupper(htmlspecialchars($user_sub_plan_name ?: 'No Plan')); ?></h2>
                <?php if ($is_active): ?>
                    <p>Your plan is active. <strong style="color:#d6eb5b;"><?php echo $days_left; ?> days remaining</strong> until <strong style="color:#d6eb5b;"><?php echo $expiry_fmt; ?></strong>.</p>
                <?php else: ?>
                    <p>Choose the perfect plan that fits your business needs. No hidden charges or extra fees.</p>
                <?php endif; ?>
                <div class="sub-hero-badge">
                    <span class="sub-hero-pill"><i class="bi bi-calendar-event"></i> Expiry: <?php echo $expiry_fmt; ?></span>
                    <span class="sub-hero-pill"><i class="bi bi-infinity"></i> Unlimited requests</span>
                    <span class="sub-hero-pill"><i class="bi bi-wallet2"></i> Wallet: ₹<?php echo number_format($userdata['wallet'] ?? 0, 2); ?></span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="sub-stat-card">
                            <div class="sub-stat-label">Status</div>
                            <div class="sub-stat-status">
                                <?php if ($is_active): ?>
                                    <div class="status-dot"></div>
                                    Active
                                <?php else: ?>
                                    <div class="status-dot" style="background:#f87171; box-shadow:0 0 8px rgba(248,113,113,0.6);"></div>
                                    <span style="color:#f87171;">Expired</span>
                                <?php endif; ?>
                            </div>
                            <div class="sub-stat-sub"><?php echo $days_left; ?> days left</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="sub-stat-card">
                            <div class="sub-stat-label">Valid Until</div>
                            <div class="sub-stat-value" style="font-size:1.1rem;"><?php echo $expiry_fmt; ?></div>
                            <div class="sub-stat-sub">Subscription expiry</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== AVAILABLE PLANS ==================== -->
    <div class="plans-section-title">
        <i class="bi bi-grid-3x3-gap-fill text-success"></i>
        Available Plans
    </div>

    <div class="row g-4 mb-4">
        <?php foreach ($all_plans as $index => $plan): 
            $is_featured = ($index === $featured_idx);
        ?>
            <div class="col-md-6 col-lg-3">
                <div class="plan-card <?php echo $is_featured ? 'featured' : ''; ?>">
                    <?php if ($is_featured): ?>
                        <div class="featured-badge">Most Popular</div>
                    <?php endif; ?>

                    <div class="plan-name"><?php echo htmlspecialchars($plan['plan_name']); ?></div>
                    <div class="plan-price">₹<?php echo number_format((int)$plan['amount']); ?></div>
                    <div class="plan-duration">/ <?php echo htmlspecialchars($plan['expiry']); ?> &nbsp;•&nbsp; One-time</div>

                    <?php if (!empty($plan['qr_requests']) || !empty($plan['txn_limit'])): ?>
                        <div class="d-flex gap-1 flex-wrap" style="margin-top:8px;">
                            <?php if (!empty($plan['qr_requests'])): ?>
                                <span style="font-size:10.5px; background:rgba(0,0,0,0.05); border-radius:6px; padding:2px 8px; font-weight:600;">
                                    <i class="bi bi-qr-code me-1"></i><?php echo htmlspecialchars($plan['qr_requests']); ?> QR
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($plan['txn_limit'])): ?>
                                <span style="font-size:10.5px; background:rgba(0,0,0,0.05); border-radius:6px; padding:2px 8px; font-weight:600;">
                                    <i class="bi bi-arrow-left-right me-1"></i><?php echo htmlspecialchars($plan['txn_limit']); ?> Txn
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <hr class="plan-divider">

                    <ul class="plan-features">
                        <?php foreach ($plan_features as [$key, $icon, $label]): 
                            $enabled = !empty($plan[$key]);
                        ?>
                            <li class="<?php echo !$enabled ? 'feat-disabled' : ''; ?>">
                                <?php if ($enabled): ?>
                                    <i class="bi bi-check-circle-fill feat-icon feat-ok"></i>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill feat-icon feat-no"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($label); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <form method="POST" action="lib/pay">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($plan['amount']); ?>">
                        <input type="hidden" name="planid" value="<?php echo htmlspecialchars($plan['id']); ?>">
                        <button type="submit" name="upigate" class="plan-btn">
                            Get <?php echo htmlspecialchars($plan['plan_name']); ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ==================== PURCHASE HISTORY ==================== -->
    <div class="history-card">
        <div class="history-card-header">
            <h6>
                <i class="bi bi-clock-history text-success"></i>
                Purchase History
            </h6>
        </div>
        <div class="table-responsive">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($history_data)): 
                        foreach ($history_data as $order):
                            $status = strtoupper($order['status'] ?? '');
                            
                            // Set status class and icon
                            $pill_class = 'status-failed';
                            $pill_icon = 'bi-x-circle-fill';
                            if ($status === 'SUCCESS') {
                                $pill_class = 'status-success';
                                $pill_icon = 'bi-check-circle-fill';
                            } elseif ($status === 'PENDING') {
                                $pill_class = 'status-pending';
                                $pill_icon = 'bi-clock-fill';
                            }
                            
                            // Get plan name
                            $plan_name = '';
                            if (!empty($order['plan_id'])) {
                                foreach ($all_plans as $plan) {
                                    if ($plan['id'] == $order['plan_id']) {
                                        $plan_name = $plan['plan_name'];
                                        break;
                                    }
                                }
                            }
                            if (empty($plan_name) && !empty($order['amount'])) {
                                $amount_int = (int)$order['amount'];
                                if (isset($plan_map[$amount_int])) {
                                    $plan_name = $plan_map[$amount_int]['plan_name'];
                                }
                            }
                            if (empty($plan_name)) {
                                $plan_name = '-';
                            }
                    ?>
                        <tr>
                            <td><span class="order-id-link"><?php echo htmlspecialchars($order['order_id'] ?? $order['id']); ?></span></td>
                            <td>
                                <span style="font-weight:600; color:#093c31;">
                                    <i class="bi bi-box-seam me-1" style="color:#d6eb5b;"></i>
                                    <?php echo htmlspecialchars($plan_name); ?>
                                </span>
                            </td>
                            <td><strong>₹<?php echo number_format((float)($order['amount'] ?? 0), 2); ?></strong></td>
                            <td><span class="status-gateway"><i class="bi bi-credit-card me-1"></i><?php echo htmlspecialchars($order['method'] ?? 'Gateway'); ?></span></td>
                            <td>
                                <span class="status-pill <?php echo $pill_class; ?>">
                                    <i class="bi <?php echo $pill_icon; ?>"></i>
                                    <?php echo ucfirst(strtolower($order['status'] ?? 'Unknown')); ?>
                                </span>
                            </td>
                            <td style="color:var(--pi-text-muted); font-size:13px;">
                                <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($order['create_date'] ?? 'now'))); ?>
                            </td>
                        </tr>
                    <?php 
                        endforeach;
                    else: 
                    ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-receipt d-block"></i>
                                    <p>No purchase history found.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>