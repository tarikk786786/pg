<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

// Fetch total users
$total_users_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='User'");
$total_users = mysqli_fetch_assoc($total_users_res)['c'];

// Fetch total orders
$total_orders_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM orders");
$total_orders = mysqli_fetch_assoc($total_orders_res)['c'];

// Fetch success vs fail
$success_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='SUCCESS'");
$success_count = mysqli_fetch_assoc($success_res)['c'];

$fail_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='FAILURE'");
$fail_count = mysqli_fetch_assoc($fail_res)['c'];

?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-graph-up me-1"></i>Analytics</span>
            <h2>Platform Reports</h2>
            <p>View detailed statistics, transaction volumes, and system health.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="transactions" class="btn btn-light fw-bold text-dark px-4 py-2" style="border-radius: 12px;">
                <i class="bi bi-download me-2"></i>Export Data
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="pi-card p-4 text-center">
            <h1 class="display-5 fw-bold text-primary mb-2"><?php echo $total_users; ?></h1>
            <p class="text-muted mb-0 fw-bold">Total Registered Merchants</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pi-card p-4 text-center">
            <h1 class="display-5 fw-bold text-success mb-2"><?php echo $total_orders; ?></h1>
            <p class="text-muted mb-0 fw-bold">Total Transactions</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pi-card p-4 text-center">
            <?php 
                $rate = $total_orders > 0 ? round(($success_count / $total_orders) * 100, 1) : 0;
            ?>
            <h1 class="display-5 fw-bold text-warning mb-2"><?php echo $rate; ?>%</h1>
            <p class="text-muted mb-0 fw-bold">Overall Success Rate</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-4">Transaction Status Overview</h5>
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-3">Quick Links</h5>
            <div class="list-group">
                <a href="transactions" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    All Transactions <i class="bi bi-arrow-right"></i>
                </a>
                <a href="merchant_list" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Merchant List <i class="bi bi-arrow-right"></i>
                </a>
                <a href="admin_payouts" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Payout Requests <i class="bi bi-arrow-right"></i>
                </a>
                <a href="support_admin" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Support Tickets <i class="bi bi-arrow-right"></i>
                </a>
                <a href="audit_logs" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Audit Logs <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Success', 'Failure', 'Pending'],
            datasets: [{
                data: [<?php echo $success_count; ?>, <?php echo $fail_count; ?>, <?php echo ($total_orders - $success_count - $fail_count); ?>],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                hoverBackgroundColor: ['#059669', '#dc2626', '#d97706'],
                borderWidth: 5,
                borderColor: '#ffffff',
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            cutout: '75%',
            layout: {
                padding: 20
            },
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: { size: 13, family: 'Inter', weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 14, family: 'Inter' },
                    bodyFont: { size: 13, family: 'Inter' },
                    cornerRadius: 8
                }
            }
        }
    });
</script>

<?php include "footer.php"; ?>
