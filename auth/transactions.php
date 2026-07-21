<?php 

// CSV Export - must run before any HTML output
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    include "config.php";
    session_start();
    if (!isset($_SESSION['username'])) { header("Location: index"); exit; }

    $mobile   = $_SESSION['username'];
    $uu       = db_query($conn, "SELECT * FROM users WHERE mobile = '$mobile'");
    $userdata = db_fetch_array($uu);

    $from    = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
    $to      = isset($_GET['to'])   ? $_GET['to']   : date('Y-m-d');
    $status  = isset($_GET['status']) ? strtoupper(trim($_GET['status'])) : '';
    $search  = isset($_GET['search']) ? trim($_GET['search']) : '';

    $from_dt = $from . " 00:00:00";
    $to_dt   = $to   . " 23:59:59";

    $user_token = $userdata['user_token'];

    // Build WHERE
    if ($userdata['role'] == 'User') {
        $where = "user_token = '$user_token' AND create_date BETWEEN '$from_dt' AND '$to_dt'";
    } else {
        $where = "create_date BETWEEN '$from_dt' AND '$to_dt'";
    }
    if (!empty($status)) {
        $where .= " AND status = '" . db_real_escape_string($conn, $status) . "'";
    }
    if (!empty($search)) {
        $s = db_real_escape_string($conn, $search);
        $where .= " AND (order_id LIKE '%$s%' OR utr LIKE '%$s%' OR customer_mobile LIKE '%$s%' OR customer_name LIKE '%$s%')";
    }

    $result = $conn->query("SELECT order_id, customer_name, customer_mobile, amount, status, method, utr, remark1, remark2, create_date FROM orders WHERE $where ORDER BY create_date DESC");

    // Output CSV headers
    $filename = "transactions_" . $from . "_to_" . $to . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // BOM for Excel UTF-8
    fputs($output, "\xEF\xBB\xBF");

    // Header row
    fputcsv($output, ['Order ID', 'Customer Name', 'Customer Mobile', 'Amount (Rs)', 'Status', 'Method', 'UTR', 'Remark 1', 'Remark 2', 'Date & Time']);

    // Data rows
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['order_id'],
                $row['customer_name'] ?? '',
                $row['customer_mobile'] ?? '',
                $row['amount'],
                $row['status'],
                $row['method'] ?? '',
                $row['utr'] ?? '',
                $row['remark1'] ?? '',
                $row['remark2'] ?? '',
                $row['create_date'],
            ]);
        }
    }

    fclose($output);
    exit;
}

include "header.php"; 

// Get date filters
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$from_dt = $from . " 00:00:00";
$to_dt = $to . " 23:59:59";

$user_token = $userdata['user_token'];

// Helper where clause
if ($userdata['role'] == 'User') {
    $where = "user_token = '$user_token' AND create_date BETWEEN '$from_dt' AND '$to_dt'";
} else {
    $where = "create_date BETWEEN '$from_dt' AND '$to_dt'";
}

// 1. Fetch Stats
$total_orders = $conn->query("SELECT COUNT(id) as count FROM orders WHERE $where")->fetch_assoc()['count'] ?? 0;
$success_data = $conn->query("SELECT SUM(amount) as amt, COUNT(id) as cnt FROM orders WHERE $where AND status = 'SUCCESS'")->fetch_assoc();
$success_amt = $success_data['amt'] ?? 0;
$success_cnt = $success_data['cnt'] ?? 0;
$pending_amt = $conn->query("SELECT SUM(amount) as amt FROM orders WHERE $where AND status = 'PENDING'")->fetch_assoc()['amt'] ?? 0;
$avg_ticket = $success_cnt > 0 ? ($success_amt / $success_cnt) : 0;

// 2. Daily Success Trend Data
$daily_labels = [];
$daily_values = [];
$start = new DateTime($from);
$end = new DateTime($to);
$end->modify('+1 day');
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($start, $interval, $end);

foreach ($period as $dt) {
    $day_str = $dt->format("Y-m-d");
    $label_str = $dt->format("d M");
    
    if ($userdata['role'] == 'User') {
        $day_where = "user_token = '$user_token' AND DATE(create_date) = '$day_str'";
    } else {
        $day_where = "DATE(create_date) = '$day_str'";
    }
    
    $day_amt = $conn->query("SELECT SUM(amount) as amt FROM orders WHERE $day_where AND status = 'SUCCESS'")->fetch_assoc()['amt'] ?? 0;
    
    $daily_labels[] = $label_str;
    $daily_values[] = floatval($day_amt);
}

// 3. Payment App Mix
if ($userdata['role'] == 'User') {
    $app_mix_query = $conn->query("SELECT method, COUNT(id) as cnt FROM orders WHERE user_token = '$user_token' AND create_date BETWEEN '$from_dt' AND '$to_dt' GROUP BY method");
} else {
    $app_mix_query = $conn->query("SELECT method, COUNT(id) as cnt FROM orders WHERE create_date BETWEEN '$from_dt' AND '$to_dt' GROUP BY method");
}
$app_mix = [];
while ($row = $app_mix_query->fetch_assoc()) {
    $app_mix[] = [
        "name" => !empty($row['method']) ? strtoupper($row['method']) : 'UPI',
        "count" => $row['cnt']
    ];
}
?>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="txnDetailModal" tabindex="-1" aria-labelledby="txnDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#093c31,#05221c);border-bottom:none;padding:18px 24px;">
                <h5 class="modal-title fw-bold text-white" id="txnDetailModalLabel">
                    <i class="bi bi-receipt me-2"></i>Transaction Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="txnDetailBody" style="background:#f8fafc;">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
            </div>
        </div>
    </div>
</div>

<div class="pi-hero-card pi-hero-card-merchant mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <span class="pi-hero-eyebrow">Merchant Reports</span>
            <h2>Inspect payment performance, export filtered data, and understand channel behavior.</h2>
            <p>
                Use this screen to review collections, success ratio, payment app mix, top VPAs,
                and recent transactions without changing the existing payment workflow.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end d-flex gap-2 justify-content-lg-end flex-wrap">
            <a href="<?php 
                $export_params = http_build_query([
                    'export' => 'csv',
                    'from'   => $from,
                    'to'     => $to,
                    'status' => $_GET['status'] ?? '',
                    'search' => $_GET['search'] ?? '',
                ]);
                echo '?' . $export_params;
            ?>" class="btn btn-pi-primary" id="exportCsvBtn">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </a>
        </div>
    </div>
</div>

<div class="pi-card p-4 mb-4">
    <form method="GET" action="" id="reportFilterForm">
        <div class="mb-3">
            <label class="form-label text-muted mb-2 small fw-bold text-uppercase tracking-wider">Quick Range</label>
            <div class="d-flex flex-wrap gap-2 w-100">
                <a href="?from=<?php echo date('Y-m-d'); ?>&to=<?php echo date('Y-m-d'); ?>" class="btn btn-sm flex-grow-1 <?php echo ($from == date('Y-m-d') && $to == date('Y-m-d')) ? 'btn-primary' : 'btn-outline-secondary'; ?>">Today</a>
                <a href="?from=<?php echo date('Y-m-d', strtotime('-7 days')); ?>&to=<?php echo date('Y-m-d'); ?>" class="btn btn-sm flex-grow-1 <?php echo ($from == date('Y-m-d', strtotime('-7 days')) && $to == date('Y-m-d')) ? 'btn-primary' : 'btn-outline-secondary'; ?>">Last 7 Days</a>
                <a href="?from=<?php echo date('Y-m-d', strtotime('-30 days')); ?>&to=<?php echo date('Y-m-d'); ?>" class="btn btn-sm flex-grow-1 <?php echo ($from == date('Y-m-d', strtotime('-30 days')) && $to == date('Y-m-d')) ? 'btn-primary' : 'btn-outline-secondary'; ?>">Last 30 Days</a>
                <a href="?from=<?php echo date('Y-m-01'); ?>&to=<?php echo date('Y-m-t'); ?>" class="btn btn-sm flex-grow-1 <?php echo ($from == date('Y-m-01') && $to == date('Y-m-t')) ? 'btn-primary' : 'btn-outline-secondary'; ?>">This Month</a>
            </div>
        </div>

        <div>
            <label class="form-label text-muted mb-2 small fw-bold text-uppercase tracking-wider">Custom Range</label>
            <div class="d-flex align-items-center gap-2 flex-wrap w-100">
                <div class="position-relative flex-grow-1">
                    <span class="position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <input type="date" class="form-control" name="from" value="<?php echo $from; ?>" style="padding-left:38px;">
                </div>
                <span class="text-muted">â†’</span>
                <div class="position-relative flex-grow-1">
                    <span class="position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <input type="date" class="form-control" name="to" value="<?php echo $to; ?>" style="padding-left:38px;">
                </div>
                <button type="submit" class="btn btn-primary flex-shrink-0">
                    <i class="bi bi-funnel me-1"></i>Apply
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="pi-stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value text-primary"><?php echo number_format($total_orders); ?></div>
            <div class="text-muted small mt-2">In selected range</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="pi-stat-card">
            <div class="stat-label">Success Amount</div>
            <div class="stat-value text-success">&#8377;<?php echo number_format($success_amt, 2); ?></div>
            <div class="text-muted small mt-2"><?php echo $success_cnt; ?> successful payments</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="pi-stat-card">
            <div class="stat-label">Pending Amount</div>
            <div class="stat-value text-warning">&#8377;<?php echo number_format($pending_amt, 2); ?></div>
            <div class="text-muted small mt-2">Awaiting completion</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="pi-stat-card">
            <div class="stat-label">Average Ticket</div>
            <div class="stat-value text-dark">&#8377;<?php echo number_format($avg_ticket, 2); ?></div>
            <div class="text-muted small mt-2">Calculated on success</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="pi-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Daily Success Trend</h6>
                <span class="pi-badge pi-badge-success">Trend Info</span>
            </div>
            <canvas id="reportTrendChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="pi-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-phone me-2 text-primary"></i>Payment App Mix</h6>
                <span class="pi-badge pi-badge-success">Live</span>
            </div>
            <div class="pi-health-list">
                <?php if (!empty($app_mix)) {
                    foreach ($app_mix as $app) { ?>
                        <div class="pi-list-compact-item">
                            <span class="fw-semibold text-dark"><?php echo htmlspecialchars($app['name']); ?></span>
                            <strong class="text-primary"><?php echo $app['count']; ?> payment(s)</strong>
                        </div>
                    <?php }
                } else { ?>
                    <div class="text-muted small py-3 text-center">No transactions in this window.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table List -->
<div class="pi-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="bi bi-receipt me-2"></i>Transactions List
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="example-table" style="width:100%">
            <thead>
                <tr>
                    <th>Date Time</th>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Mobile</th>
                    <th>Method/App</th>
                    <th>UTR</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($userdata['role'] == 'User') {
                    $table_query = "SELECT * FROM `orders` WHERE user_token = '$user_token' AND create_date BETWEEN '$from_dt' AND '$to_dt' ORDER BY id DESC";
                } else {
                    $table_query = "SELECT * FROM `orders` WHERE create_date BETWEEN '$from_dt' AND '$to_dt' ORDER BY id DESC";
                }
                $table_run = db_query($conn, $table_query);

                if ($table_run && db_num_rows($table_run) > 0) {
                    while ($row = db_fetch_assoc($table_run)) {
                        $status = strtoupper($row['status']);
                        $status_class = ($status == "SUCCESS") ? "pi-badge pi-badge-success" : (($status == "PENDING") ? "pi-badge pi-badge-warning" : "pi-badge pi-badge-danger");
                        ?>
                        <tr>
                            <td class="small"><?php echo !empty($row['create_date']) ? date("d M, h:i A", strtotime($row['create_date'])) : '-'; ?></td>
                            <td><code class="fw-semibold text-dark"><?php echo htmlspecialchars($row['order_id']); ?></code></td>
                            <td class="fw-bold">&#8377;<?php echo number_format($row['amount'], 2); ?></td>
                            <td><span class="<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                            <td><?php echo htmlspecialchars($row['customer_mobile']); ?></td>
                            <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($row['method'] ?? 'UPI'); ?></span></td>
                            <td><code style="font-size:11px;"><?php echo !empty($row['utr']) ? htmlspecialchars($row['utr']) : '-'; ?></code></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary py-1 px-3 txn-view-btn fw-semibold" 
                                        style="font-size:11.5px;" 
                                        data-id="<?php echo $row['id']; ?>"
                                        data-url="get_transaction_details.php?id=<?php echo $row['id']; ?>">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No transactions found for the selected range.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Success Trend Chart Setup
    new Chart(document.getElementById('reportTrendChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($daily_labels); ?>,
            datasets: [{
                label: 'Success Amount',
                data: <?php echo json_encode($daily_values); ?>,
                fill: true,
                backgroundColor: 'rgba(197, 230, 56, 0.14)',
                borderColor: '#c5e638',
                borderWidth: 2,
                tension: 0.35,
                pointBackgroundColor: '#c5e638',
                pointRadius: 3.5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => '\u20B9' + Number(value).toLocaleString() } },
                x: { grid: { display: false } }
            }
        }
    });

    // Transaction Details AJAX and Modal Handling
    document.querySelectorAll('.txn-view-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.dataset.url;
            const modal = new bootstrap.Modal(document.getElementById('txnDetailModal'));
            document.getElementById('txnDetailBody').innerHTML =
                '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
            modal.show();

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    const statusColor = d.status === 'SUCCESS' ? '#16a34a' : (d.status === 'FAILURE' ? '#dc2626' : '#d97706');
                    const statusBg    = d.status === 'SUCCESS' ? '#dcfce7' : (d.status === 'FAILURE' ? '#fee2e2' : '#fef3c7');

                    function cell(label, value, mono = false, full = false) {
                        const colClass = full ? 'col-12' : 'col-6 col-md-4';
                        const valStyle = mono
                            ? 'font-family:monospace;font-size:12px;word-break:break-all;overflow-wrap:anywhere;white-space:normal;'
                            : 'word-break:break-word;overflow-wrap:anywhere;';
                        return `
                        <div class="${colClass}">
                            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:12px 14px;height:100%;">
                                <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">${label}</div>
                                <div style="color:#0f172a;font-weight:600;font-size:13px;${valStyle}">${value || ' '}</div>
                            </div>
                        </div>`;
                    }

                    let payerName = '';
                    if (d.description && d.description.includes('Payer: ')) {
                        payerName = d.description.split('Payer: ')[1] || '';
                    }

                    const screenshotUrl = d.screenshot 
                        ? ((d.screenshot.startsWith('http://') || d.screenshot.startsWith('https://')) ? d.screenshot : `/${d.screenshot}`)
                        : null;

                    document.getElementById('txnDetailBody').innerHTML = `
                    <div style="padding:0;">
                        <div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                            <div>
                                <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">Order ID</div>
                                <div style="font-family:monospace;font-size:15px;font-weight:700;color:#0f172a;word-break:break-all;">${d.order_id}</div>
                            </div>
                            <span style="background:${statusBg};color:${statusColor};font-size:12px;font-weight:700;padding:6px 16px;border-radius:20px;letter-spacing:.5px;">
                                ${d.status}
                            </span>
                        </div>

                        <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);padding:16px 20px;border-bottom:1px solid #bbf7d0;display:flex;align-items:center;gap:12px;">
                            <div style="background:#fff;border-radius:50%;width:42px;height:42px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(22,163,74,.2);">
                                <i class="bi bi-currency-rupee" style="color:#16a34a;font-size:18px;"></i>
                            </div>
                            <div>
                                <div style="color:#166534;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Amount Paid</div>
                                <div style="color:#14532d;font-size:22px;font-weight:800;line-height:1;">&#8377;${parseFloat(d.amount).toFixed(2)}</div>
                            </div>
                        </div>

                        <div style="padding:16px 14px;">
                            <div class="row g-2">
                                ${cell('UTR / Ref No.', d.utr, true)}
                                ${cell('Payment App', d.payment_app)}
                                ${cell('UPI VPA (Sender)', d.vpa, true)}
                                ${cell('Paid By (Name)', payerName)}
                                ${cell('Customer Mobile', d.customer_mobile)}
                                ${cell('Customer Email', d.customer_email)}
                                ${cell('Gateway TXN ID', d.gateway_txn && d.gateway_txn !== '' ? d.gateway_txn : d.order_id, true)}
                                ${cell('Bank Order ID', d.bank_orderid, true)}
                                ${cell('Remark', (d.remark1 || '') + (d.remark2 && d.remark2 !== '' ? ' / ' + d.remark2 : ''))}
                                ${cell('Created At (IST)', d.created_at)}
                                ${cell('Updated At (IST)', d.updated_at)}
                                ${d.description && d.description !== '' ? cell('Description', d.description, false, true) : ''}
                            </div>
                            
                            ${screenshotUrl ? `
                            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-top:10px;">
                                <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Payment Screenshot</div>
                                <div class="text-center">
                                    <a href="${screenshotUrl}" target="_blank">
                                        <img src="${screenshotUrl}" style="max-width:100%;max-height:300px;border-radius:8px;border:1px solid #cbd5e1;cursor:zoom-in;" />
                                    </a>
                                </div>
                            </div>` : ''}
                        </div>

                        <div style="background:#fff;border-top:1px solid #e2e8f0;padding:16px 20px;display:${d.status !== 'SUCCESS' ? 'block' : 'none'};">
                            <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;"><i class="bi bi-shield-check me-1"></i>Manual Verification Action</div>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-5">
                                    <label style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">UTR Number</label>
                                    <input type="text" id="manualApproveUtr" class="form-control form-control-sm" placeholder="Enter 12-digit UTR" value="${d.utr && d.utr !== '' ? d.utr : ''}" />
                                </div>
                                <div class="col-sm-4">
                                    <label style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Success Redirect URL</label>
                                    <input type="url" id="manualApproveRedirectUrl" class="form-control form-control-sm" placeholder="Optional Custom Redirect URL" value="${d.redirect_url && d.redirect_url !== '' ? d.redirect_url : ''}" />
                                </div>
                                <div class="col-sm-3 d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-success flex-grow-1" onclick="submitManualApproval(${d.id}, 'SUCCESS')" style="font-size:11.5px;font-weight:600;padding:6px 10px;">Approve</button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="submitManualApproval(${d.id}, 'FAILURE')" style="font-size:11.5px;font-weight:600;padding:6px 10px;">Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                })
                .catch(() => {
                    document.getElementById('txnDetailBody').innerHTML =
                        '<div class="alert alert-danger m-3">Failed to load transaction details. Please try again.</div>';
                });
        });
    });

    function submitManualApproval(orderId, status) {
        const utr = document.getElementById('manualApproveUtr')?.value.trim();
        const redirectUrl = document.getElementById('manualApproveRedirectUrl')?.value.trim();

        if (status === 'SUCCESS' && (!utr || utr.length < 10)) {
            alert('Please enter a valid UTR (at least 10 characters) to approve.');
            return;
        }

        if (!confirm('Are you sure you want to mark this transaction as ' + status + '?')) {
            return;
        }

        const data = {
            order_id: orderId,
            status: status,
            utr: utr,
            redirect_url: redirectUrl
        };

        fetch('update_transaction_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert(res.message || 'Approval failed.');
            }
        })
        .catch(err => {
            alert('Server error occurred.');
        });
    }
</script>

<!-- AI Support Widget (Forest Theme) -->
<div id="ai-widget-container">
    <button id="ai-toggle-btn" class="shadow-lg d-flex align-items-center justify-content-center">
        <i class="bi bi-robot fs-4 text-white"></i>
    </button>
    <div id="ai-chat-window" class="card shadow-lg d-none">
        <div class="card-header bg-white border-bottom p-0">
            <div class="d-flex justify-content-between align-items-center p-3 pb-2">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-stars me-2"></i>Dezo Assistant</h6>
                <div class="d-flex gap-2">
                     <button class="btn btn-sm btn-light rounded-circle" id="ai-close-btn"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body p-0 d-flex flex-column" style="height: 400px;">
            <div id="ai-messages-area" class="flex-grow-1 p-3 overflow-auto bg-light">
                <div class="text-center mt-3">
                    <span class="bg-primary-subtle text-primary rounded-circle p-3 d-inline-block mb-2">
                        <i class="bi bi-robot fs-2"></i>
                    </span>
                    <h6 class="fw-bold">Dezo Support Bot</h6>
                    <p class="text-muted small">Ask me any question regarding your dashboard, integrations, or subscription status.</p>
                </div>
            </div>
            <div class="p-3 border-top bg-white">
                <form id="ai-chat-form" class="d-flex gap-2">
                    <input type="text" id="ai-input" class="form-control rounded-pill" placeholder="Type a message..." autocomplete="off">
                    <button type="submit" class="btn btn-primary rounded-circle" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-send-fill text-white"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #ai-widget-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; }
    #ai-toggle-btn { width: 60px; height: 60px; border-radius: 50%; border: none; background: var(--pi-sidebar-bg); transition: transform 0.2s; }
    #ai-toggle-btn:hover { transform: scale(1.05); }
    #ai-chat-window { position: absolute; bottom: 80px; right: 0; width: 350px; border-radius: 16px; border: none; display: flex; flex-direction: column; }
    .ai-message { padding: 10px 14px; border-radius: 14px; max-width: 85%; font-size: 0.9rem; margin-bottom: 10px; }
    .ai-message.system { align-self: flex-start; background: #e2e8f0; color: #0f172a; }
    .ai-message.user { align-self: flex-end; background: var(--pi-sidebar-bg); color: white; }
</style>

<script>
    (function() {
        const toggleBtn = document.getElementById('ai-toggle-btn');
        const chatWindow = document.getElementById('ai-chat-window');
        const closeBtn = document.getElementById('ai-close-btn');
        const chatForm = document.getElementById('ai-chat-form');
        const messagesArea = document.getElementById('ai-messages-area');
        const inputEl = document.getElementById('ai-input');

        if (toggleBtn && chatWindow) {
            toggleBtn.addEventListener('click', () => chatWindow.classList.toggle('d-none'));
            closeBtn.addEventListener('click', () => chatWindow.classList.add('d-none'));

            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const val = inputEl.value.trim();
                if(!val) return;

                // User message
                appendMsg(val, 'user');
                inputEl.value = '';

                // Simulating support bot reply
                setTimeout(() => {
                    let reply = "I'm looking into this for you. For instant checkout assistance, please verify your merchant status or check transaction logs.";
                    if(val.toLowerCase().includes('wallet')) {
                        reply = "Your wallet balance is settled directly to your connected bank account. Please verify bank reports.";
                    } else if(val.toLowerCase().includes('status')) {
                        reply = "You can update transaction status manually using the 'Approve/Reject' buttons inside transaction details.";
                    }
                    appendMsg(reply, 'system');
                }, 700);
            });
        }

        function appendMsg(txt, sender) {
            const div = document.createElement('div');
            div.className = `ai-message ${sender}`;
            div.innerText = txt;
            messagesArea.appendChild(div);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        // Refresh page every 2 seconds to fetch latest real-time transactions data
        // Do not refresh if any modal is open
        setTimeout(function() {
            if (!$('.modal').hasClass('show')) {
                window.location.reload();
            }
        }, 2000);
    })();
</script>

<?php include "footer.php"; ?>
