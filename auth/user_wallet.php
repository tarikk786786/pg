<?php 
include "header.php"; 

// Role restriction removed so admin can test their wallet

$user_id = $userdata['id'];

// Handle Payout Request
if (isset($_POST['request_payout'])) {
    $amount = (float) $_POST['amount'];
    
    if ($amount < 500) {
        $error = "Minimum payout amount is ₹500.";
    } elseif ($amount > (float)$userdata['wallet']) {
        $error = "Insufficient wallet balance.";
    } else {
        // Deduct from wallet and insert request
        mysqli_begin_transaction($conn);
        try {
            $deduct = "UPDATE users SET wallet = wallet - $amount WHERE id = $user_id AND wallet >= $amount";
            $res = mysqli_query($conn, $deduct);
            
            if (mysqli_affected_rows($conn) > 0) {
                $insert = "INSERT INTO payout_requests (user_id, amount, status) VALUES ($user_id, $amount, 'Pending')";
                mysqli_query($conn, $insert);
                mysqli_commit($conn);
                $success = "Payout request submitted successfully. It will be processed soon.";
                $userdata['wallet'] -= $amount; // update local variable
            } else {
                throw new Exception("Wallet deduction failed.");
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Failed to process request. Try again later.";
        }
    }
}

// Fetch history
$history = mysqli_query($conn, "SELECT * FROM payout_requests WHERE user_id = $user_id ORDER BY id DESC LIMIT 50");
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-wallet2 me-1"></i>My Wallet</span>
            <h2>Wallet & Settlements</h2>
            <p>Manage your earnings, request payouts, and view your withdrawal history.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <h3 class="fw-bold mb-0 text-warning">₹<?php echo number_format($userdata['wallet'] ?? 0, 2); ?></h3>
            <div class="small">Available Balance</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-cash-coin text-success me-2"></i>Request Payout</h5>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success py-2"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Withdrawal Amount (Min: ₹500)</label>
                    <input type="number" name="amount" min="500" step="1" class="form-control" required placeholder="e.g. 1000">
                </div>
                <button type="submit" name="request_payout" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-send-check me-1"></i> Submit Request
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="pi-card">
            <div class="p-4 border-bottom">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Payout History</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Req ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Requested</th>
                            <th>Date Processed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($history) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($history)): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?php echo $row['id']; ?></td>
                                    <td class="fw-bold text-dark">₹<?php echo number_format($row['amount'], 2); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Pending'): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                        <?php elseif ($row['status'] == 'Approved'): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted"><?php echo date('d M Y, h:i A', strtotime($row['requested_at'])); ?></td>
                                    <td class="small text-muted"><?php echo $row['processed_at'] ? date('d M Y, h:i A', strtotime($row['processed_at'])) : '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No payout requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
