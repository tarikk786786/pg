<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

if (isset($_POST['update_status'])) {
    $req_id = (int)$_POST['req_id'];
    $status = db_real_escape_string($conn, $_POST['status']);
    
    db_begin_transaction($conn);
    try {
        $check = db_query($conn, "SELECT * FROM payout_requests WHERE id = $req_id AND status = 'Pending'");
        if ($req = db_fetch_assoc($check)) {
            $user_id = $req['user_id'];
            $amount = $req['amount'];

            // Update request status
            db_query($conn, "UPDATE payout_requests SET status = '$status', processed_at = CURRENT_TIMESTAMP WHERE id = $req_id");
            
            if ($status == 'Rejected') {
                // Refund wallet
                db_query($conn, "UPDATE users SET wallet = wallet + $amount WHERE id = $user_id");
            }
            db_commit($conn);
            $success = "Payout request #$req_id marked as $status.";
        } else {
            throw new Exception("Invalid or already processed request.");
        }
    } catch (Exception $e) {
        db_rollback($conn);
        $error = $e->getMessage();
    }
}

$requests = db_query($conn, "SELECT p.*, u.name, u.mobile, u.company FROM payout_requests p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC");
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-bank me-1"></i>Admin Action</span>
            <h2>Payout Management</h2>
            <p>Review and process wallet withdrawal requests from merchants.</p>
        </div>
    </div>
</div>

<div class="pi-card p-4">
    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-list-ul text-primary me-2"></i>All Payout Requests</h5>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger py-2"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success py-2"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>Req ID</th>
                    <th>Merchant</th>
                    <th>Amount</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (db_num_rows($requests) > 0): ?>
                    <?php while($row = db_fetch_assoc($requests)): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($row['mobile']); ?></div>
                            </td>
                            <td class="fw-bold text-dark">₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td class="small text-muted"><?php echo date('d M Y, h:i A', strtotime($row['requested_at'])); ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif ($row['status'] == 'Approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <div class="d-flex gap-2">
                                        <form method="POST" class="m-0">
                                            <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="status" value="Approved">
                                            <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form method="POST" class="m-0">
                                            <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="status" value="Rejected">
                                            <button class="btn btn-sm btn-danger" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="small text-muted">Processed on<br><?php echo date('d M, h:i A', strtotime($row['processed_at'])); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "footer.php"; ?>
