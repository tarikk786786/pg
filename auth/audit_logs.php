<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

$logs = db_query($conn, "SELECT a.*, u.name as admin_name FROM audit_logs a JOIN users u ON a.user_id = u.id ORDER BY a.id DESC LIMIT 100");
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-shield-lock me-1"></i>Security & History</span>
            <h2>System Audit Logs</h2>
            <p>Track all administrative actions, updates, and changes made within the system.</p>
        </div>
    </div>
</div>

<div class="pi-card p-4">
    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Recent Activity</h5>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>Log ID</th>
                    <th>Date & Time</th>
                    <th>Action By</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (db_num_rows($logs) > 0): ?>
                    <?php while($row = db_fetch_assoc($logs)): ?>
                        <tr>
                            <td class="small text-muted">#<?php echo $row['id']; ?></td>
                            <td class="small text-muted"><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($row['admin_name']); ?></span></td>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['action']); ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($row['details']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No audit logs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "footer.php"; ?>
